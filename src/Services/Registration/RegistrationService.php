<?php

declare(strict_types=1);

namespace App\Services\Registration;

use App\Entity\User;
use App\Security\EmailVerifier;
use App\Services\Email\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationService implements RegistrationServiceInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $em,
        private EmailVerifier $emailVerifier,
        private EmailService $emailService,
    ) {}

    public function register(User $user, string $plainPassword): void
    {
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));

        $this->em->persist($user);
        $this->em->flush();

        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            (new TemplatedEmail())
                ->from(new Address('no-reply@fanread.fr', 'FanRead Email Bot'))
                ->to((string) $user->getEmail())
                ->subject('Confirmez votre adresse email')
                ->htmlTemplate('emails/confirmation_email.html')
        );
    }

    public function verifyEmail(Request $request, User $user): void
    {
        $this->emailVerifier->handleEmailConfirmation($request, $user);

        $this->emailService->sendWelcome(
            to: (string) $user->getEmail(),
            firstname: $user->getFirstname() ?? 'Lecteur',
        );
    }
}
