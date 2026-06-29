<?php

namespace App\Tests\Security;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $container = static::getContainer();

        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);
        $this->userRepository = $container->get(UserRepository::class);

        foreach ($this->userRepository->findAll() as $user) {
            $em->remove($user);
        }

        $em->flush();
    }

    public function testRegister(): void
    {
        $this->client->request('GET', '/inscription');
        self::assertResponseIsSuccessful();
        self::assertPageTitleContains('FanRead | Inscription');

        $this->client->submitForm('Créer mon compte', [
            'registration_form[firstname]' => 'John',
            'registration_form[lastname]' => 'Doe',
            'registration_form[email]' => 'me@example.com',
            'registration_form[plainPassword]' => 'password',
            'registration_form[agreeTerms]' => true,
        ]);

        self::assertResponseRedirects();
        self::assertCount(1, $this->userRepository->findAll());

        $user = $this->userRepository->findAll()[0];
        self::assertFalse($user->isVerified());

        self::assertEmailCount(1);

        $email = self::getMailerMessage(0);
        self::assertNotNull($email);
        self::assertEmailAddressContains($email, 'from', 'no-reply@fanread.fr');
        self::assertEmailAddressContains($email, 'to', 'me@example.com');

        $this->client->followRedirect();
        $this->client->loginUser($user);

        /** @var TemplatedEmail $templatedEmail */
        $templatedEmail = $email;
        $messageBody = $templatedEmail->getHtmlBody();
        self::assertIsString($messageBody);

        preg_match('#(http://localhost/verification/email[^"]+)#', $messageBody, $verifyLink);
        self::assertNotEmpty($verifyLink, 'Lien de vérification introuvable dans le corps de l\'email.');

        $this->client->request('GET', $verifyLink[1]);
        self::assertResponseRedirects('/connexion');

        $this->client->followRedirect();
        self::assertTrue(
            static::getContainer()->get(UserRepository::class)->findAll()[0]->isVerified()
        );
    }
}
