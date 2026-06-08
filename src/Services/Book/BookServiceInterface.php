<?php

declare(strict_types=1);

namespace App\Services\Book;

use App\Entity\Book;
use App\Entity\User;

interface BookServiceInterface
{
    /**
     * Crée un livre et l'associe à un utilisateur.
     * Initialise les valeurs par défaut (addedAt, pagesRead, isFavorite, status).
     */
    public function create(Book $book, User $user): Book;
}
