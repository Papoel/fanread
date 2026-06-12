<?php

declare(strict_types=1);

namespace App\Services\Book;

use App\Entity\Book;
use App\Entity\User;
use App\Enum\Book\Status;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;

final class BookService implements BookServiceInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly BookRepository         $bookRepository,
    ) {}

    public function create(Book $book, User $user): Book
    {
        $book
            ->setUser($user)
            ->setAddedAt(new \DateTimeImmutable())
            ->setStatus(Status::InProgress)
            ->setPagesRead(0)
            ->setRating(0)
            ->setIsFavorite(false);

        $this->em->persist($book);
        $this->em->flush();

        return $book;
    }

    public function findByUser(User $user): array
    {
        return $this->bookRepository->findByUser($user);
    }

    public function findByUserFiltered(
        User   $user,
        string $tab      = 'all',
        string $status   = 'all',
        string $category = 'all',
        string $sort     = 'recent'
    ): array {
        return $this->bookRepository->findByUserFiltered($user, $tab, $status, $category, $sort);
    }
}
