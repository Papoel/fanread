<?php

namespace App\Services\Book\Isbn\Api\Interface;

use App\Dto\BookData;
use App\Exception\IsbnApiException;

/**
 * Interface pour les providers ISBN
 *
 * Définit le contrat que tous les services de recherche ISBN doivent respecter.
 * Cela permet de basculer entre différents providers (ISBNdb, Google Books, etc.)
 * sans modifier le code client.
 *
 * Principes appliqués:
 * - Dependency Inversion (D - SOLID): Les clients dépendent de cette interface
 * - Interface Segregation (I - SOLID): Interface petite et spécifique
 * - Open/Closed (O - SOLID): Ouvert à l'extension via nouvelles implémentations
 *
 * @package App\Service\Book
 */
interface IsbnProviderInterface
{
    /**
     * Récupère les informations complètes d'un livre par son ISBN
     *
     * @param string $isbn L'ISBN du livre à rechercher (format ISBN-10 ou ISBN-13)
     *
     * @return BookData Les données du livre trouvé
     *
     * @throws IsbnApiException Si l'ISBN est invalide ou si la requête API échoue
     *
     * @example
     * try {
     *     $book = $provider->getBook('9780140449136');
     *     echo $book->title; // "The Hobbit"
     * } catch (IsbnApiException $e) {
     *     echo $e->getMessage();
     * }
     */
    public function getBook(string $isbn): BookData;

    /**
     * Valide le format d'un ISBN
     *
     * Cette méthode vérifie que l'ISBN est valide sans faire de requête API.
     * Accepte les formats ISBN-10 et ISBN-13, avec ou sans tirets.
     *
     * @param string $isbn L'ISBN à valider
     *
     * @return bool true si l'ISBN est valide, false sinon
     *
     * @example
     * $provider->isValidIsbn('0140449136');      // true (ISBN-10)
     * $provider->isValidIsbn('9780140449136');   // true (ISBN-13)
     * $provider->isValidIsbn('978-0-14-044913-6'); // true (avec tirets)
     * $provider->isValidIsbn('invalid');         // false
     */
    public function isValidIsbn(string $isbn): bool;
}
