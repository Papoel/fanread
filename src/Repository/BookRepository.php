<?php

namespace App\Repository;

use App\Entity\Book;
use App\Entity\User;
use App\Enum\Book\Category;
use App\Enum\Book\Status;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * @return list<Book>
     */
    public function findByUser(User $user): array
    {
        /** @var list<Book> $result */
        $result = $this->createQueryBuilder('b')
            ->where('b.user = :user')
            ->setParameter('user', $user)
            ->orderBy('b.addedAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $result;
    }

    /**
     * Filtre + trie les livres d'un utilisateur.
     *
     * @return list<Book>
     */
    public function findByUserFiltered(
        User    $user,
        string  $tab      = 'all',
        string  $status   = 'all',
        string  $category = 'all',
        string  $sort     = 'recent'
    ): array {
        $qb = $this->createQueryBuilder('b')
            ->where('b.user = :user')
            ->setParameter('user', $user);

        if ($tab === 'favorites') {
            $qb->andWhere('b.isFavorite = true');
        }

        if ($status !== 'all') {
            $statusEnum = Status::tryFrom($status);
            if ($statusEnum) {
                $qb->andWhere('b.status = :status')
                    ->setParameter('status', $statusEnum->value);
            }
        }

        if ($category !== 'all') {
            $categoryEnum = Category::tryFrom($category);
            if ($categoryEnum) {
                $qb->andWhere('b.category = :category')
                    ->setParameter('category', $categoryEnum->value);
            }
        }

        match ($sort) {
            'title'  => $qb->orderBy('b.title', 'ASC'),
            'rating' => $qb->orderBy('b.rating', 'DESC')->addOrderBy('b.addedAt', 'DESC'),
            default  => $qb->orderBy('b.addedAt', 'DESC'),
        };

        /** @var list<Book> $result */
        $result = $qb->getQuery()->getResult();

        return $result;
    }

    public function countByUser(User $user): int
    {
        return (int) $this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->where('b.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
