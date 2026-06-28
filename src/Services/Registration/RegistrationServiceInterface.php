<?php

declare(strict_types=1);

namespace App\Services\Registration;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

interface RegistrationServiceInterface
{
    public function register(User $user, string $plainPassword): void;

    public function verifyEmail(Request $request, User $user): void;
}
