<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Dto\BookData;
use App\Exception\IsbnApiException;
use App\Services\Book\Isbn\Api\GoogleBooksService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class GoogleBooksServiceTest extends TestCase
{
    #[Test]
    public function returnsBookDataForKnownIsbn(): void
    {
        $payload = json_encode([
            'totalItems' => 1,
            'items' => [[
                'id' => 'abc123',
                'volumeInfo' => [
                    'title' => 'Le Petit Prince',
                    'authors' => ['Antoine de Saint-Exupéry'],
                    'pageCount' => 96,
                    'imageLinks' => ['thumbnail' => 'http://example.com/cover.jpg'],
                ],
            ]],
        ], JSON_THROW_ON_ERROR);

        $service = new GoogleBooksService(
            new MockHttpClient(new MockResponse($payload)),
            new NullLogger(),
        );

        $result = $service->getBook('978-2-07-061275-8');

        self::assertInstanceOf(BookData::class, $result);
        self::assertSame('Le Petit Prince', $result->title);
        self::assertSame('Antoine de Saint-Exupéry', $result->author);
        self::assertSame(96, $result->getPageCount());
        self::assertSame('http://example.com/cover.jpg', $result->imageUrl);
    }

    #[Test]
    public function throwsWhenNoResult(): void
    {
        $service = new GoogleBooksService(
            new MockHttpClient(new MockResponse('{"totalItems":0,"items":[]}')),
            new NullLogger(),
        );

        $this->expectException(IsbnApiException::class);
        $service->getBook('978-2-07-061275-8');
    }

    #[Test]
    public function throwsForInvalidIsbn(): void
    {
        $service = new GoogleBooksService(new MockHttpClient(), new NullLogger());

        $this->expectException(IsbnApiException::class);
        $service->getBook('   ');
    }
}
