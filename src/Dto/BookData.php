<?php

namespace App\Dto;

class BookData
{
    public function __construct(
        public readonly string $isbn,
        public readonly string $title,
        public readonly ?string $author = null,
        public readonly ?string $publisher = null,
        public readonly ?string $publishDate = null,
        public readonly ?string $imageUrl = null,
        /** @var array<string, mixed> */
        public readonly array $data = [],
    ) {}

    /**
     * Créer une instance depuis les données brutes de l'API
     *
     * @param array<string, mixed> $data
     */
    public static function fromApiResponse(array $data): self
    {
        return new self(
            isbn: is_string($data['isbn'] ?? null) ? $data['isbn'] : '',
            title: is_string($data['title'] ?? null) ? $data['title'] : 'Unknown',
            author: is_string($data['author'] ?? null) ? $data['author'] : null,
            publisher: is_string($data['publisher'] ?? null) ? $data['publisher'] : null,
            publishDate: is_string($data['date_published'] ?? null) ? $data['date_published'] : null,
            imageUrl: is_string($data['image'] ?? null) ? $data['image'] : null,
            data: $data,
        );
    }

    /**
     * @return array<string, string|null>
     */
    public function toArray(): array
    {
        return [
            'isbn' => $this->isbn,
            'title' => $this->title,
            'author' => $this->author,
            'publisher' => $this->publisher,
            'publishDate' => $this->publishDate,
            'imageUrl' => $this->imageUrl,
        ];
    }

    public function getPageCount(): ?int
    {
        $pageCount = $this->data['pageCount'] ?? null;

        return is_numeric($pageCount) ? (int) $pageCount : null;
    }
}
