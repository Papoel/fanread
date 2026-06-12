<?php

declare(strict_types=1);

namespace App\Twig\Components\Card;

use App\Entity\Book;
use App\Entity\User;
use App\Enum\Book\Status;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('BookCardComponent', template: 'components/card/BookCardComponent.html.twig')]
class BookCardComponent
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    /** L'entité est stockée par ID et rechargée automatiquement à chaque re-render. */
    #[LiveProp]
    public Book $book;

    /** Quand true, le composant affiche une div vide (carte supprimée). */
    #[LiveProp]
    public bool $deleted = false;

    /** Demander la confirmation avant de supprimer le livre. */
    #[LiveProp]
    public bool $confirmingDelete = false;

    public function __construct(private readonly Security $security) {}

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
