<?php

namespace App\Tests\Controller\Books;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class BooksControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);
        /** @var UserPasswordHasherInterface $hasher */
        $hasher = $container->get(UserPasswordHasherInterface::class);

        // Nettoyage pour éviter les collisions d'email
        foreach ($em->getRepository(User::class)->findBy(['email' => 'book-tester@example.com']) as $existing) {
            $em->remove($existing);
        }
        $em->flush();

        $user = (new User())->setEmail('book-tester@example.com');
        $user->setPassword($hasher->hashPassword($user, 'password'));
        $em->persist($user);
        $em->flush();

        $client->loginUser($user);
        $client->request(Request::METHOD_GET, '/books');

        self::assertResponseIsSuccessful();
    }
}
