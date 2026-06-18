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
        public readonly array $data = [],
    ) {}

    /**
     * Créer une instance depuis les données brutes de l'API
     */
    public static function fromApiResponse(array $data): self
    {
        return new self(
            isbn: $data['isbn'] ?? '',
            title: $data['title'] ?? 'Unknown',
            author: $data['author'] ?? null,
            publisher: $data['publisher'] ?? null,
            publishDate: $data['date_published'] ?? null,
            imageUrl: $data['image'] ?? null,
            data: $data,
        );
    }

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
}
