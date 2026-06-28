<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Entity\User;

interface ProfileServiceInterface
{
    public function updateInfo(User $user): void;
    public function updatePassword(User $user, string $currentPassword, string $newPassword): void;
}
