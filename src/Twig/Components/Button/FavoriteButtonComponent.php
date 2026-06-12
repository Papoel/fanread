<?php

declare(strict_types=1);

namespace App\Twig\Components\Button;

use App\Entity\Book;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('FavoriteButtonComponent', template: 'components/button/FavoriteButtonComponent.html.twig')]
class FavoriteButtonComponent
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    #[LiveProp]
    public Book $book;

    public function __construct(private readonly Security $security) {}

    #[LiveAction]
    public function toggle(EntityManagerInterface $em): void
    {
        /** @var User $currentUser */
        $currentUser = $this->security->getUser();
        $bookOwner   = $this->book->getUser();

        if (null === $bookOwner || $bookOwner->getId() !== $currentUser->getId()) {
            throw new AccessDeniedHttpException(
                'Vous ne pouvez pas modifier un livre qui ne vous appartient pas.'
            );
        }

        $this->book->setIsFavorite(!$this->book->isFavorite());
        $em->flush();

        $this->emit('book:toast', [
            'type'    => 'success',
            'message' => $this->book->isFavorite()
                ? 'Ajouté à vos coups de cœur !'
                : 'Retiré des coups de cœur.',
        ]);
    }
}
