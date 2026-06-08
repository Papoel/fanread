<?php

namespace App\Controller\Book;

use App\Entity\Book;
use App\Form\Book\BookFormType;
use App\Services\Book\BookServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class BookController extends AbstractController
{
    public function __construct(
        private readonly BookServiceInterface $bookService
    ) {}

    #[Route('/books', name: 'app_book', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $book = new Book();
        $form = $this->createForm(BookFormType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bookService->create($book, $this->getUser());
            $this->addFlash('success', 'Livre ajouté à votre bibliothèque !');

            return $this->redirectToRoute('app_book');
        }

        return $this->render('book/index.html.twig', [
            'bookForm'  => $form,
            'showForm'  => ($form->isSubmitted() && !$form->isValid())
                || $request->query->has('showForm'),
            'activeTab' => $request->query->get('tab', 'all'),
        ]);
    }
}
