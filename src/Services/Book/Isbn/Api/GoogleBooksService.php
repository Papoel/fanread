<?php

namespace App\Services\Book\Isbn\Api;

use App\Dto\BookData;
use App\Exception\IsbnApiException;
use App\Services\Book\Isbn\Api\Interface\IsbnProviderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GoogleBooksService implements IsbnProviderInterface
{
    private readonly string $apiUrl;
    private readonly string $apiKey;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
        string $googleBooksUrl = 'https://www.googleapis.com/books/v1/volumes',
        string $googleBooksKey = '',
    ) {
        $this->apiUrl = rtrim($googleBooksUrl, '/');
        $this->apiKey = $googleBooksKey;
    }

    public function getBook(string $isbn): BookData
    {
        if (!$this->isValidIsbn($isbn)) {
            throw new IsbnApiException(sprintf('ISBN invalide: %s', $isbn));
        }

        try {
            $query = ['q' => sprintf('isbn:%s', $isbn)];
            if (!empty($this->apiKey)) {
                $query['key'] = $this->apiKey;
            }

            $response = $this->httpClient->request(
                Request::METHOD_GET,
                $this->apiUrl,
                [
                    'query' => $query,
                    'headers' => ['Accept' => 'application/json'],
                    'timeout' => 10,
                ]
            );

            if ($response->getStatusCode() !== 200) {
                throw IsbnApiException::fromHttpError(
                    $response->getStatusCode(),
                    $response->getContent(false)
                );
            }

            $data = $response->toArray();

            if (($data['totalItems'] ?? 0) === 0 || empty($data['items'])) {
                throw new IsbnApiException(
                    sprintf('Aucun livre trouvé pour l\'ISBN: %s', $isbn)
                );
            }

            $this->logger->info('Livre Google Books récupéré', ['isbn' => $isbn]);

            $items = $data['items'];
            $item  = is_array($items) ? ($items[0] ?? null) : null;
            if (!is_array($item)) {
                throw new IsbnApiException(
                    sprintf('Aucun livre trouvé pour l\'ISBN: %s', $isbn)
                );
            }

            /** @var array<string, mixed> $volumeInfo */
            $volumeInfo = is_array($item['volumeInfo'] ?? null) ? $item['volumeInfo'] : [];

            // pageCount souvent absent dans la recherche → on enrichit via la fiche complète
            $volumeId = $item['id'] ?? null;
            if (empty($volumeInfo['pageCount']) && is_string($volumeId) && $volumeId !== '') {
                $volumeInfo = $this->fetchVolumeDetails($volumeId) ?? $volumeInfo;
            }

            return $this->mapToBookData($isbn, $volumeInfo);

        } catch (\Throwable $e) {
            $this->logger->error('Erreur Google Books', [
                'isbn' => $isbn,
                'error' => $e->getMessage(),
            ]);

            if ($e instanceof IsbnApiException) {
                throw $e;
            }

            throw IsbnApiException::fromException($e);
        }
    }

    public function isValidIsbn(string $isbn): bool
    {
        $isbn = preg_replace('/[^0-9X]/', '', strtoupper($isbn)) ?? '';

        return match (strlen($isbn)) {
            10 => $this->validateIsbn10($isbn),
            13 => $this->validateIsbn13($isbn),
            default => false,
        };
    }

    /**
     * @return array<string, mixed>|null
     */
    private function fetchVolumeDetails(string $volumeId): ?array
    {
        try {
            $query = [];
            if (!empty($this->apiKey)) {
                $query['key'] = $this->apiKey;
            }

            $response = $this->httpClient->request(
                Request::METHOD_GET,
                sprintf('%s/%s', $this->apiUrl, $volumeId),
                [
                    'query'   => $query,
                    'headers' => ['Accept' => 'application/json'],
                    'timeout' => 10,
                ]
            );

            if ($response->getStatusCode() !== 200) {
                return null;
            }

            /** @var array<string, mixed>|null $volumeInfo */
            $volumeInfo = $response->toArray()['volumeInfo'] ?? null;

            return is_array($volumeInfo) ? $volumeInfo : null;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @param array<string, mixed> $volumeInfo
     */
    private function mapToBookData(string $isbn, array $volumeInfo): BookData
    {
        $authors    = $volumeInfo['authors'] ?? null;
        $imageLinks = $volumeInfo['imageLinks'] ?? null;
        $thumbnail  = is_array($imageLinks) ? ($imageLinks['thumbnail'] ?? null) : null;

        return new BookData(
            isbn: $isbn,
            title: is_string($volumeInfo['title'] ?? null) ? $volumeInfo['title'] : 'Unknown',
            author: is_array($authors) && ($names = array_filter($authors, 'is_string')) !== []
                ? implode(', ', $names)
                : null,
            publisher: is_string($volumeInfo['publisher'] ?? null) ? $volumeInfo['publisher'] : null,
            publishDate: is_string($volumeInfo['publishedDate'] ?? null) ? $volumeInfo['publishedDate'] : null,
            imageUrl: is_string($thumbnail) ? $thumbnail : null,
            data: $volumeInfo,
        );
    }

    private function validateIsbn10(string $isbn): bool
    {
        if (!preg_match('/^[0-9]{9}[0-9X]$/', $isbn)) {
            return false;
        }
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $digit = $isbn[$i] === 'X' ? 10 : (int) $isbn[$i];
            $sum += $digit * (10 - $i);
        }
        return $sum % 11 === 0;
    }

    private function validateIsbn13(string $isbn): bool
    {
        if (!preg_match('/^[0-9]{13}$/', $isbn)) {
            return false;
        }
        $sum = 0;
        for ($i = 0; $i < 13; $i++) {
            $weight = ($i % 2 === 0) ? 1 : 3;
            $sum += (int) $isbn[$i] * $weight;
        }
        return $sum % 10 === 0;
    }
}
