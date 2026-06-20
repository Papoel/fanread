<?php

declare(strict_types=1);

namespace App\Services\Book;

use App\Entity\Book;
use App\Entity\User;

interface BookServiceInterface
{
    public function create(Book $book, User $user): Book;

    /** @return Book[] */
    public function findByUser(User $user): array;

    /** @return Book[] */
    public function findByUserFiltered(
        User $user,
        string $tab = 'all',
        string $status = 'all',
        string $category = 'all',
        string $sort = 'recent',
    ): array;

    public function countByUser(User $user): int;
}
