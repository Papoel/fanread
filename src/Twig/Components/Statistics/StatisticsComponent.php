<?php

declare(strict_types=1);

namespace App\Twig\Components\Statistics;

use App\Entity\Book;
use App\Entity\User;
use App\Enum\Book\Category;
use App\Enum\Book\Status;
use App\Repository\BookRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent('StatisticsComponent', template: 'components/statistics/StatisticsComponent.html.twig')]
class StatisticsComponent
{
    use DefaultActionTrait;

    /** Type de graphique pour la répartition par statut (doughnut|pie|bar). */
    #[LiveProp(writable: true)]
    public string $statusChartType = Chart::TYPE_DOUGHNUT;

    /** @var list<Book>|null */
    private ?array $booksCache = null;

    public function __construct(
        private readonly BookRepository $bookRepository,
        private readonly ChartBuilderInterface $chartBuilder,
        private readonly Security $security,
    ) {
    }

    /**
     * @return list<Book>
     */
    private function books(): array
    {
        if ($this->booksCache !== null) {
            return $this->booksCache;
        }

        $user = $this->security->getUser();

        return $this->booksCache = $user instanceof User
            ? $this->bookRepository->findByUser($user)
            : [];
    }

    #[ExposeInTemplate]
    public function getTotalBooks(): int
    {
        return count($this->books());
    }

    #[ExposeInTemplate]
    public function getFinishedCount(): int
    {
        return $this->countByStatus(Status::Finish);
    }

    #[ExposeInTemplate]
    public function getInProgressCount(): int
    {
        return $this->countByStatus(Status::InProgress);
    }

    #[ExposeInTemplate]
    public function getNotStartedCount(): int
    {
        return $this->countByStatus(Status::NotStarted);
    }

    #[ExposeInTemplate]
    public function getFavoritesCount(): int
    {
        return count(array_filter($this->books(), static fn (Book $b): bool => $b->isFavorite() === true));
    }

    #[ExposeInTemplate]
    public function getTotalPagesRead(): int
    {
        return array_sum(array_map(static fn (Book $b): int => $b->getPagesRead() ?? 0, $this->books()));
    }

    #[ExposeInTemplate]
    public function getAverageRating(): float
    {
        $rated = array_filter($this->books(), static fn (Book $b): bool => $b->getRating() !== null && $b->getRating() > 0);

        if ($rated === []) {
            return 0.0;
        }

        $sum = array_sum(array_map(static fn (Book $b): int => (int) $b->getRating(), $rated));

        return round($sum / count($rated), 1);
    }

    /**
     * Répartition par statut (doughnut/pie/bar selon $statusChartType).
     */
    public function getStatusChart(): Chart
    {
        $labels = [];
        $values = [];

        foreach (Status::cases() as $status) {
            $labels[] = $status->label();
            $values[] = $this->countByStatus($status);
        }

        $chart = $this->chartBuilder->createChart($this->statusChartType);
        $chart->setData([
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Livres',
                'data' => $values,
                'backgroundColor' => ['#f43f5e', '#fb923c', '#22c55e'],
                'borderColor' => '#ffffff',
                'borderWidth' => 2,
            ]],
        ]);
        $chart->setOptions([
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => ['legend' => ['position' => 'bottom']],
        ]);

        return $chart;
    }

    /**
     * Top catégories (barres horizontales).
     */
    public function getCategoryChart(): Chart
    {
        $counts = [];

        foreach ($this->books() as $book) {
            $category = $book->getCategory() ?? Category::Unclassified;
            $counts[$category->value] = ($counts[$category->value] ?? 0) + 1;
        }

        arsort($counts);
        $counts = array_slice($counts, 0, 8, true);

        $labels = [];
        foreach (array_keys($counts) as $value) {
            $labels[] = Category::from((string) $value)->label();
        }

        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Livres par catégorie',
                'data' => array_values($counts),
                'backgroundColor' => '#f43f5e',
                'borderRadius' => 6,
            ]],
        ]);
        $chart->setOptions([
            'responsive' => true,
            'maintainAspectRatio' => false,
            'indexAxis' => 'y',
            'plugins' => ['legend' => ['display' => false]],
            'scales' => ['x' => ['ticks' => ['precision' => 0]]],
        ]);

        return $chart;
    }

    /**
     * Livres ajoutés par mois (année en cours).
     */
    public function getMonthlyChart(): Chart
    {
        $year = (int) date('Y');
        $months = array_fill(1, 12, 0);

        foreach ($this->books() as $book) {
            $addedAt = $book->getAddedAt();
            if ($addedAt !== null && (int) $addedAt->format('Y') === $year) {
                $months[(int) $addedAt->format('n')]++;
            }
        }

        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels' => ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
            'datasets' => [[
                'label' => 'Ajouts en ' . $year,
                'data' => array_values($months),
                'borderColor' => '#f43f5e',
                'backgroundColor' => 'rgba(244, 63, 94, 0.15)',
                'fill' => true,
                'tension' => 0.35,
                'pointBackgroundColor' => '#f43f5e',
            ]],
        ]);
        $chart->setOptions([
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => ['legend' => ['display' => false]],
            'scales' => ['y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]],
        ]);

        return $chart;
    }

    private function countByStatus(Status $status): int
    {
        return count(array_filter($this->books(), static fn (Book $b): bool => $b->getStatus() === $status));
    }
}
