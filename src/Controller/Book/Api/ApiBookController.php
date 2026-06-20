<?php

namespace App\Controller\Book\Api;

use App\Exception\IsbnApiException;
use App\Services\Book\Isbn\Api\Interface\IsbnProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/books', name: 'app_books_')]
class ApiBookController extends AbstractController
{
    public function __construct(
        private readonly IsbnProviderInterface $isbnProvider,
    ) {
    }

    #[Route('/{isbn}', name: 'show', methods: ['GET'])]
    public function show(string $isbn): Response
    {
        try {
            $book = $this->isbnProvider->getBook($isbn);

            return new JsonResponse(
                $book->toArray(),
                Response::HTTP_OK
            );
        } catch (IsbnApiException $e) {
            return new JsonResponse(
                [
                    'error' => $e->getMessage(),
                    'isbn' => $isbn,
                ],
                Response::HTTP_BAD_REQUEST
            );
        } catch (\Throwable $e) {
            return new JsonResponse(
                ['error' => 'Une erreur interne s\'est produite'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/search', name: 'search', methods: ['POST'])]
    public function search(Request $request): Response
    {
        $isbn = (string) $request->request->get('isbn');

        if (!$isbn) {
            return new JsonResponse(
                ['error' => 'L\'ISBN est requis'],
                Response::HTTP_BAD_REQUEST
            );
        }

        if (!$this->isbnProvider->isValidIsbn($isbn)) {
            return new JsonResponse(
                ['error' => 'Format ISBN invalide'],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $book = $this->isbnProvider->getBook($isbn);

            return new JsonResponse(
                $book->toArray(),
                Response::HTTP_OK
            );
        } catch (IsbnApiException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }
    }
}
