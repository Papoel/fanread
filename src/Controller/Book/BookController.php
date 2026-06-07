<?php

namespace App\Controller\Book;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BookController extends AbstractController
{
    #[Route('/books', name: 'app_book')]
    public function index(Request $request): Response
    {
        $activeTab = $request->query->get('tab', 'all');

        return $this->render('book/index.html.twig', [
            'activeTab' => $activeTab,
        ]);
    }
}
