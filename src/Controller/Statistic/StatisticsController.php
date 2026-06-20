<?php

declare(strict_types=1);

namespace App\Controller\Statistic;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class StatisticsController extends AbstractController
{
    #[Route('/statistics', name: 'app_statistics')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render('statistics/index.html.twig');
    }
}
