<?php

namespace App\Entity;

use App\Enum\Book\Category;
use App\Enum\Book\Status;
use App\Repository\BookRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $title = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $author = null;

    #[ORM\Column(nullable: true)]
    private ?int $totalPages = null;

    #[ORM\Column(nullable: true)]
    private ?int $pagesRead = null;

    #[ORM\Column(nullable: true)]
    private ?int $rating = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $review = null;

    #[ORM\Column(type: Types::STRING, nullable: true, enumType: Status::class)]
    private ?Status $status = Status::NotStarted;

    #[ORM\Column]
    private ?\DateTimeImmutable $addedAt = null;

    #[ORM\Column]
    private ?bool $isFavorite = null;

    #[ORM\Column(type: Types::STRING, nullable: true, enumType: Category::class)]
    private ?Category $category = Category::Unclassified;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $isbn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $coverUrl = null;

    #[ORM\ManyToOne(inversedBy: 'books')]
    private ?User $user = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getTotalPages(): ?int
    {
        return $this->totalPages;
    }

    public function setTotalPages(?int $totalPages): static
    {
        $this->totalPages = $totalPages;

        return $this;
    }

    public function getPagesRead(): ?int
    {
        return $this->pagesRead;
    }

    public function setPagesRead(?int $pagesRead): static
    {
        $this->pagesRead = $pagesRead;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getReview(): ?string
    {
        return $this->review;
    }

    public function setReview(?string $review): static
    {
        $this->review = $review;

        return $this;
    }

    /**
     * @return Status
     */
    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getAddedAt(): ?\DateTimeImmutable
    {
        return $this->addedAt;
    }

    public function setAddedAt(\DateTimeImmutable $addedAt): static
    {
        $this->addedAt = $addedAt;

        return $this;
    }

    public function isFavorite(): ?bool
    {
        return $this->isFavorite;
    }

    public function setIsFavorite(bool $isFavorite): static
    {
        $this->isFavorite = $isFavorite;

        return $this;
    }

    /**
     * @return Category|null
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(?string $isbn): static
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getCoverUrl(): ?string
    {
        return $this->coverUrl;
    }

    public function setCoverUrl(?string $coverUrl): static
    {
        $this->coverUrl = $coverUrl;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }
}
