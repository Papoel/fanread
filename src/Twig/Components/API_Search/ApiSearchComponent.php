<?php

declare(strict_types=1);

namespace App\Twig\Components\API_Search;

use App\Entity\Book;
use App\Entity\User;
use App\Exception\IsbnApiException;
use App\Form\Book\BookFormType;
use App\Services\Book\BookServiceInterface;
use App\Services\Book\Isbn\Api\Interface\IsbnProviderInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsLiveComponent('ApiSearchComponent', template: 'components/api_search/ApiSearchComponent.html.twig')]
class ApiSearchComponent
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public ?Book $book = null;

    #[LiveProp]
    public string $statusMessage = '';

    #[LiveProp]
    public string $statusType = '';

    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly IsbnProviderInterface $isbnProvider,
        private readonly BookServiceInterface $bookService,
        private readonly Security $security,
    ) {}

    #[PostMount]
    public function postMount(): void
    {
        $this->book ??= new Book();
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->formFactory->create(BookFormType::class, $this->book ?? new Book());
    }

    #[LiveAction]
    public function search(): void
    {
        $isbn = trim((string) ($this->formValues['isbn'] ?? ''));

        if ($isbn === '') {
            $this->setStatus('error', 'Veuillez entrer un ISBN.');
            return;
        }

        try {
            $found = $this->isbnProvider->getBook($isbn);
        } catch (IsbnApiException) {
            $this->setStatus('error', 'Aucun livre trouvé avec cet ISBN.');
            return;
        }

        // Remplit uniquement les champs encore vides
        $this->fillIfEmpty('title', $found->title);
        $this->fillIfEmpty('author', $found->author);
        $this->fillIfEmpty('coverUrl', $found->imageUrl);
        $this->fillIfEmpty('totalPages', $found->data['pageCount'] ?? null);

        $this->setStatus('success', 'Informations trouvées avec succès !');
    }

    #[LiveAction]
    public function save(UrlGeneratorInterface $urlGenerator): RedirectResponse
    {
        $this->submitForm(); // valide le formulaire (lève une exception si invalide)

        /** @var Book $book */
        $book = $this->getForm()->getData();

        /** @var User $user */
        $user = $this->security->getUser();

        $this->bookService->create($book, $user);

        return new RedirectResponse($urlGenerator->generate('app_book'));
    }

    private function fillIfEmpty(string $field, int|string|null $value): void
    {
        if ($value !== null && $value !== '' && empty($this->formValues[$field])) {
            $this->formValues[$field] = (string) $value;
        }
    }

    private function setStatus(string $type, string $message): void
    {
        $this->statusType = $type;
        $this->statusMessage = $message;
    }
}
