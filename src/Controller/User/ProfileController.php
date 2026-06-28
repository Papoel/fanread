<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User;
use App\Form\User\ChangePasswordFormType;
use App\Form\User\ProfileFormType;
use App\Services\User\ProfileServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    public function __construct(
        private readonly ProfileServiceInterface $profileService,
    ) {
    }

    #[Route('/profil', name: 'app_profile', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('user/profile/index.html.twig', [
            'profileForm' => $this->createForm(ProfileFormType::class, $this->getUser()),
            'changePasswordForm' => $this->createForm(ChangePasswordFormType::class),
        ]);
    }

    #[Route('/profil/informations', name: 'app_profile_info', methods: ['POST'])]
    public function updateInfo(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(ProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->profileService->updateInfo($user);
            $this->addFlash('info', 'Profil mis à jour.');
        }

        return $this->redirectToRoute('app_profile');
    }

    #[Route('/profil/mot-de-passe', name: 'app_profile_password', methods: ['POST'])]
    public function updatePassword(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                /** @var string $currentPassword */
                $currentPassword = $form->get('currentPassword')->getData();
                /** @var string $newPassword */
                $newPassword = $form->get('newPassword')->getData();

                $this->profileService->updatePassword($user, $currentPassword, $newPassword);
            } catch (\InvalidArgumentException $e) {
                $this->addFlash('error_password', $e->getMessage());
            }
        }

        return $this->redirectToRoute('app_profile');
    }
}
