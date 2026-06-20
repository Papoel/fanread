<?php

declare(strict_types=1);

namespace App\Twig\Components\Toast;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

/**
 * @phpstan-type Toast array{id: string, type: string, message: string, createdAt: int}
 */
#[AsLiveComponent('ToastComponent', template: 'components/toast/ToastComponent.html.twig')]
class ToastComponent
{
    use DefaultActionTrait;

    /** @var list<Toast> */
    #[LiveProp]
    public array $toasts = [];

    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function mount(): void
    {
        $session = $this->requestStack->getSession();
        if (!$session instanceof FlashBagAwareSessionInterface) {
            return;
        }

        /** @var array<string, list<string>> $flashes */
        $flashes = $session->getFlashBag()->all();

        foreach ($flashes as $key => $messages) {
            foreach ($messages as $message) {
                $this->toasts[] = [
                    'id' => uniqid('', true),
                    'type' => $this->normalizeType($key),
                    'message' => $message,
                    'createdAt' => time(),
                ];
            }
        }
    }

    private function normalizeType(string $key): string
    {
        return match (true) {
            str_contains($key, 'error') => 'error',
            str_contains($key, 'success') => 'success',
            str_contains($key, 'warning') => 'warning',
            default => 'info',
        };
    }

    #[LiveListener('book:toast')]
    public function addToast(#[LiveArg] string $type, #[LiveArg] string $message): void
    {
        $this->toasts[] = [
            'id' => uniqid(),
            'type' => $type,
            'message' => $message,
            'createdAt' => time(),
        ];
    }

    #[LiveAction]
    public function prune(): void
    {
        $now = time();
        $kept = [];

        foreach ($this->toasts as $toast) {
            if (($now - $toast['createdAt']) < 8) {
                $kept[] = $toast;
            }
        }

        $this->toasts = $kept;
    }

    #[LiveAction]
    public function removeToast(#[LiveArg] string $id): void
    {
        $kept = [];

        foreach ($this->toasts as $toast) {
            if ($toast['id'] !== $id) {
                $kept[] = $toast;
            }
        }

        $this->toasts = $kept;
    }
}
