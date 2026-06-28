<?php

declare(strict_types=1);

namespace App\Services\Email;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class EmailService
{
    private const FROM_EMAIL = 'bot@fanread.fr';

    public function __construct(
        private readonly MailerInterface $mailer,
    ) {
    }

    /**
     * Envoie un email avec un template Twig
     *
     * @param string $to Adresse email du destinataire
     * @param string $subject Sujet de l'email
     * @param string $template Chemin du template (ex: 'emails/welcome.html')
     * @param array<string, mixed> $context Variables à passer au template
     * @param string|null $toName Nom du destinataire (optionnel)
     *
     * @throws TransportExceptionInterface
     */
    public function send(
        string $to,
        string $subject,
        string $template,
        array $context = [],
        ?string $toName = null,
    ): void {
        $email = (new TemplatedEmail())
            ->from(self::FROM_EMAIL)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context);

        if ($toName) {
            $email->to(sprintf('%s <%s>', $toName, $to));
        }

        $this->mailer->send($email);
    }

    /**
     * Envoie un email de bienvenue à un nouvel utilisateur
     *
     * @param string $to Adresse email du destinataire
     * @param string $firstname Prénom de l'utilisateur
     * @param string $shopUrl URL de la boutique
     *
     * @throws TransportExceptionInterface
     */
    public function sendWelcome(string $to, string $firstname): void
    {
        $this->send(
            to: $to,
            subject: 'Bienvenue sur FanRead',
            template: 'emails/welcome.html',
            context: [
                'firstname' => $firstname,
            ],
            toName: $firstname
        );
    }

    /**
     * Envoie un email de réinitialisation de mot de passe
     *
     * @param string $to Adresse email du destinataire
     * @param string $firstname Prénom de l'utilisateur
     * @param string $lastname Nom de l'utilisateur
     * @param string $resetLink Lien de réinitialisation
     *
     * @throws TransportExceptionInterface
     */
    public function sendPasswordReset(
        string $to,
        string $firstname,
        string $lastname,
        string $resetLink,
    ): void {
        $this->send(
            to: $to,
            subject: 'Réinitialisation de votre mot de passe sur FanRead',
            template: 'emails/forgot_password.html',
            context: [
                'firstname' => $firstname,
                'lastname' => $lastname,
                'link' => $resetLink,
            ],
            toName: sprintf('%s %s', $firstname, $lastname)
        );
    }
}
