<?php

declare(strict_types=1);

namespace App\Controller\Book;

use App\Entity\Book;
use App\Entity\User;
use App\Enum\Book\Category;
use App\Form\Book\BookFormType;
use App\Services\Book\BookServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class BookController extends AbstractController
{
    public function __construct(
        private readonly BookServiceInterface $bookService,
    ) {
    }

    #[Route('/books', name: 'app_book', methods: ['GET'])]
    public function index(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render(
            'book/index.html.twig',
            $this->buildViewData($user, $request, $this->createForm(BookFormType::class, new Book()))
        );
    }

    #[Route('/books', name: 'app_book_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $book = new Book();
        $form = $this->createForm(BookFormType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bookService->create($book, $user);
            $this->addFlash('success', 'Livre ajouté à votre bibliothèque !');

            return $this->redirectToRoute('app_book');
        }

        return $this->render(
            'book/index.html.twig',
            $this->buildViewData($user, $request, $form, true)
        );
    }

    private function buildViewData(
        User $user,
        Request $request,
        FormInterface $form,
        bool $forceShowForm = false,
    ): array {
        $activeTab      = $request->query->get('tab', 'all');
        $filterStatus   = $request->query->get('status', 'all');
        $filterCategory = $request->query->get('category', 'all');
        $sortBy         = $request->query->get('sort', 'recent');

        return [
            'bookForm'       => $form,
            'showForm'       => $forceShowForm || $request->query->has('showForm'),
            'activeTab'      => $activeTab,
            'filterStatus'   => $filterStatus,
            'filterCategory' => $filterCategory,
            'sortBy'         => $sortBy,
            'books'          => $this->bookService->countByUser($user),
            'sortedBooks'    => $this->bookService->findByUserFiltered($user, $activeTab, $filterStatus, $filterCategory, $sortBy),
            'categories'     => Category::cases(),
        ];
    }
}
