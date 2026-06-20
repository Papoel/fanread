<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User;
use App\Form\User\ChangePasswordFormType;
use App\Form\User\ProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    #[Route('/profil', name: 'app_profile', methods: ['GET'])]
    public function index(): Response
    {
        $profileForm = $this->createForm(ProfileFormType::class, $this->getUser());
        $changePasswordForm = $this->createForm(ChangePasswordFormType::class);

        return $this->render('user/profile/index.html.twig', [
            'profileForm' => $profileForm,
            'changePasswordForm' => $changePasswordForm,
        ]);
    }

    #[Route('/profil/informations', name: 'app_profile_info', methods: ['POST'])]
    public function updateInfo(Request $request, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(ProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUpdatedAt(new \DateTimeImmutable());
            $em->flush();
            $this->addFlash('info', 'Profil mis à jour.');
        }

        return $this->redirectToRoute('app_profile');
    }

    #[Route('/profil/mot-de-passe', name: 'app_profile_password', methods: ['POST'])]
    public function updatePassword(
        Request $request,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $em,
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $current */
            $current = $form->get('currentPassword')->getData();
            /** @var string $new */
            $new = $form->get('newPassword')->getData();
            /** @var string $confirm */
            $confirm = $form->get('confirmPassword')->getData();

            if (!$hasher->isPasswordValid($user, $current)) {
                $this->addFlash('error_password', 'Le mot de passe actuel est incorrect.');

                return $this->redirectToRoute('app_profile');
            }

            if ($new !== $confirm) {
                $this->addFlash('error_password', 'Les deux mots de passe ne correspondent pas.');

                return $this->redirectToRoute('app_profile');
            }

            $user->setPassword($hasher->hashPassword($user, $new));
            $user->setUpdatedAt(new \DateTimeImmutable());
            $em->flush();

            $this->addFlash('success_password', 'Votre mot de passe a été modifié avec succès.');
        }

        return $this->redirectToRoute('app_profile');
    }
}
