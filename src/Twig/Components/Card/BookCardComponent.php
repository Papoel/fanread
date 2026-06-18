<?php

declare(strict_types=1);

namespace App\Twig\Components\Card;

use App\Entity\Book;
use App\Entity\User;
use App\Enum\Book\Category;
use App\Enum\Book\Status;
use App\Exception\IsbnApiException;
use App\Services\Book\Isbn\Api\Interface\IsbnProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('BookCardComponent', template: 'components/card/BookCardComponent.html.twig')]
class BookCardComponent
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    /** Champs éditables en live (titre, auteur, pages, etc.). */
    #[LiveProp(writable: ['pagesRead', 'title', 'author', 'totalPages', 'isbn', 'coverUrl', 'review'])]
    public Book $book;

    /** Catégorie gérée en scalaire (évite l'hydratation enum sur chemin imbriqué). */
    #[LiveProp(writable: true)]
    public ?string $categoryValue = null;

    #[LiveProp]
    public bool $deleted = false;

    #[LiveProp]
    public bool $confirmingDelete = false;

    /** Modale "Modifier". */
    #[LiveProp]
    public bool $editing = false;

    /** Modale "Avis & Notes". */
    #[LiveProp]
    public bool $reviewing = false;

    #[LiveProp]
    public string $isbnStatus = '';

    #[LiveProp]
    public string $isbnStatusType = '';

    public function __construct(
        private readonly Security $security,
        private readonly IsbnProviderInterface $isbnProvider,
    ) {}

    public function getProgressPercentage(): int
    {
        $total = $this->book->getTotalPages() ?? 0;
        $read  = $this->book->getPagesRead() ?? 0;

        return $total > 0 ? (int) round(min($read / $total * 100, 100)) : 0;
    }

    /** @return Category[] */
    public function getCategories(): array
    {
        return Category::cases();
    }

    #[LiveAction]
    public function openEdit(): void
    {
        $this->assertOwnership();
        $this->categoryValue = $this->book->getCategory()?->value;
        $this->editing = true;
    }

    #[LiveAction]
    public function openReview(): void
    {
        $this->assertOwnership();
        $this->reviewing = true;
    }

    #[LiveAction]
    public function closeModals(): void
    {
        $this->editing   = false;
        $this->reviewing = false;
    }

    #[LiveAction]
    public function setRating(#[LiveArg] int $rating): void
    {
        $this->assertOwnership();
        $this->book->setRating(max(0, min(5, $rating)));
    }

    #[LiveAction]
    public function searchIsbn(): void
    {
        $this->assertOwnership();

        $isbn = trim((string) $this->book->getIsbn());
        if ($isbn === '') {
            $this->setIsbnStatus('error', 'Veuillez saisir un ISBN.');
            return;
        }

        try {
            $found = $this->isbnProvider->getBook($isbn);
        } catch (IsbnApiException) {
            $this->setIsbnStatus('error', 'Aucun livre trouvé avec cet ISBN.');
            return;
        }

        // Mise à jour des champs depuis l'API
        if ($found->title !== '' && $found->title !== 'Unknown') {
            $this->book->setTitle($found->title);
        }
        if ($found->author) {
            $this->book->setAuthor($found->author);
        }
        if (!empty($found->data['pageCount'])) {
            $this->book->setTotalPages((int) $found->data['pageCount']);
        }
        if ($found->imageUrl) {
            $this->book->setCoverUrl($found->imageUrl);
        }

        $this->setIsbnStatus('success', 'Informations mises à jour. Cliquez sur Enregistrer pour sauvegarder.');
    }

    private function setIsbnStatus(string $type, string $message): void
    {
        $this->isbnStatusType = $type;
        $this->isbnStatus     = $message;
    }

    #[LiveAction]
    public function saveEdit(EntityManagerInterface $em): void
    {
        $this->assertOwnership();

        $total = $this->book->getTotalPages() ?? 0;
        $read  = max(0, $this->book->getPagesRead() ?? 0);
        if ($total > 0) {
            $read = min($read, $total);
        }
        $this->book->setPagesRead($read);

        if (null !== $this->categoryValue) {
            $this->book->setCategory(Category::tryFrom($this->categoryValue));
        }

        $this->book->setStatus(match (true) {
            $total > 0 && $read >= $total => Status::Finish,
            $read > 0                     => Status::InProgress,
            default                       => Status::NotStarted,
        });

        $em->flush();
        $this->editing = false;

        $this->emit('book:toast', [
            'type'    => 'success',
            'message' => 'Livre mis à jour.',
        ]);
    }

    #[LiveAction]
    public function saveReview(EntityManagerInterface $em): void
    {
        $this->assertOwnership();

        $em->flush();
        $this->reviewing = false;

        $this->emit('book:toast', [
            'type'    => 'success',
            'message' => 'Avis et note enregistrés.',
        ]);
    }

    #[LiveAction]
    public function toggleStatus(EntityManagerInterface $em): void
    {
        $this->assertOwnership();

        $newStatus = $this->book->getStatus() === Status::Finish
            ? ($this->book->getPagesRead() > 0 ? Status::InProgress : Status::NotStarted)
            : Status::Finish;

        $this->book->setStatus($newStatus);
        $em->flush();

        $message = match ($newStatus) {
            Status::Finish     => 'Livre marqué comme terminé !',
            Status::InProgress => 'Lecture reprise.',
            Status::NotStarted => 'Remis dans la liste de lecture.',
        };

        $this->emit('book:toast', [
            'type'    => 'success',
            'message' => $message,
        ]);
    }

    #[LiveAction]
    public function askDelete(): void
    {
        $this->confirmingDelete = true;
    }

    #[LiveAction]
    public function cancelDelete(): void
    {
        $this->confirmingDelete = false;
    }

    #[LiveAction]
    public function delete(EntityManagerInterface $em): void
    {
        $this->assertOwnership();

        $em->remove($this->book);
        $em->flush();

        $this->deleted = true;

        $this->emit('book:toast', [
            'type'    => 'warning',
            'message' => 'Livre retiré de votre bibliothèque.',
        ]);
    }

    private function assertOwnership(): void
    {
        /** @var User $currentUser */
        $currentUser = $this->security->getUser();
        $bookOwner   = $this->book->getUser();

        if (null === $bookOwner || $bookOwner->getId() !== $currentUser->getId()) {
            throw new AccessDeniedHttpException(
                'Vous ne pouvez pas modifier un livre qui ne vous appartient pas.'
            );
        }
    }
}
