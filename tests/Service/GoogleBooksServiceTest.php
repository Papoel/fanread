<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Services\Book\Isbn\GoogleBooksService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class GoogleBooksServiceTest extends TestCase
{
    #[Test] public function returnsBookDataForKnownIsbn(): void
    {
        $payload = json_encode([
            'items' => [[
                'volumeInfo' => [
                    'title'      => 'Le Petit Prince',
                    'authors'    => ['Antoine de Saint-Exupéry'],
                    'pageCount'  => 96,
                    'imageLinks' => ['thumbnail' => 'http://example.com/cover.jpg'],
                ],
            ]],
        ], JSON_THROW_ON_ERROR);

        $service = new GoogleBooksService(new MockHttpClient(new MockResponse($payload)));
        $result  = $service->searchByIsbn('978-2-07-061275-8');

        self::assertSame('Le Petit Prince', $result['title']);
        self::assertSame('Antoine de Saint-Exupéry', $result['author']);
        self::assertSame(96, $result['pageCount']);
        self::assertSame('https://example.com/cover.jpg', $result['coverUrl']);
    }

    #[Test] public function returnsNullWhenNoResult(): void
    {
        $service = new GoogleBooksService(new MockHttpClient(new MockResponse('{}')));
        self::assertNull($service->searchByIsbn('0000000000'));
    }

    #[Test] public function returnsNullForEmptyIsbn(): void
    {
        $service = new GoogleBooksService(new MockHttpClient());
        self::assertNull($service->searchByIsbn('   '));
    }
}
