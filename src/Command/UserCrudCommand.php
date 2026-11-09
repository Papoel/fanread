<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:user',
    description: 'CRUD des utilisateurs : list, create, show, update, delete',
)]
class UserCrudCommand extends Command
{
    private const ACTIONS = ['list', 'create', 'show', 'update', 'delete', 'help'];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('action', InputArgument::OPTIONAL, 'Action : '.implode('|', self::ACTIONS))
            ->addArgument('identifier', InputArgument::OPTIONAL, 'Id ou email de l\'utilisateur (show, update, delete)')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Supprime sans demander confirmation')
            ->setHelp(<<<'HELP'
                La commande <info>%command.name%</info> permet de gérer les utilisateurs en CRUD.

                  <info>php %command.full_name%</info>                        Menu interactif
                  <info>php %command.full_name% list</info>                 Liste tous les utilisateurs
                  <info>php %command.full_name% create</info>               Crée un utilisateur (prompts)
                  <info>php %command.full_name% show john@test.fr</info>    Affiche un utilisateur
                  <info>php %command.full_name% update 3</info>             Modifie l'utilisateur #3
                  <info>php %command.full_name% delete john@test.fr</info>  Supprime après confirmation
                  <info>php %command.full_name% delete 3 --force</info>     Supprime sans confirmation
                  <info>php %command.full_name% help</info>                 Affiche cette aide

                L'<comment>identifier</comment> accepte un id numérique ou une adresse email.
                HELP);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string|null $action */
        $action = $input->getArgument('action');
        if (null === $action) {
            if (!$input->isInteractive()) {
                $io->error('Précise une action : '.implode('|', self::ACTIONS));

                return Command::INVALID;
            }
            $action = $io->choice('Que veux-tu faire ?', self::ACTIONS, 'list');
        }

        if (!\in_array($action, self::ACTIONS, true)) {
            $io->error(sprintf('Action "%s" inconnue. Actions possibles : %s.', $action, implode(', ', self::ACTIONS)));

            return Command::INVALID;
        }

        /** @var string|null $identifier */
        $identifier = $input->getArgument('identifier');

        return match ($action) {
            'help' => $this->showHelp($io),
            'list' => $this->listUsers($io),
            'create' => $this->createUser($input, $io),
            'show', 'update', 'delete' => $this->runOnUser($action, $identifier, $input, $io),
        };
    }

    private function listUsers(SymfonyStyle $io): int
    {
        $users = $this->userRepository->findBy([], ['id' => 'ASC']);

        if ([] === $users) {
            $io->warning('Aucun utilisateur en base.');

            return Command::SUCCESS;
        }

        $io->title(sprintf('%d utilisateur(s)', \count($users)));
        $io->table(
            ['Id', 'Email', 'Pseudo', 'Rôles', 'Vérifié', 'Créé le'],
            array_map(static fn (User $user): array => [
                $user->getId(),
                $user->getEmail(),
                $user->getPseudo() ?? '-',
                implode(', ', $user->getRoles()),
                $user->isVerified() ? 'oui' : 'non',
                $user->getCreatedAt()?->format('d/m/Y H:i') ?? '-',
            ], $users),
        );

        return Command::SUCCESS;
    }

    private function createUser(InputInterface $input, SymfonyStyle $io): int
    {
        $io->title('Création d\'un utilisateur');

        $email = $this->askEmail($input, $io);
        if (null === $email) {
            return Command::FAILURE;
        }

        if (null !== $this->userRepository->findOneBy(['email' => $email])) {
            $io->error(sprintf('Un compte existe déjà avec l\'adresse "%s".', $email));

            return Command::FAILURE;
        }

        $plainPassword = $this->askPassword($input, $io, 'Mot de passe');
        if (null === $plainPassword) {
            return Command::FAILURE;
        }

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));

        if ($input->isInteractive()) {
            $user->setFirstname($io->ask('Prénom', null));
            $user->setLastname($io->ask('Nom', null));
            $user->setPseudo($io->ask('Pseudo', null));
            $user->setRoles($this->askRoles($io, $user->getRoles()));
            $user->setIsPseudoDisplayed($io->confirm('Afficher le pseudo publiquement ?', false));
            $user->setIsVerified($io->confirm('Compte déjà vérifié ?', true));
        } else {
            $user->setIsVerified(true);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success(sprintf('Utilisateur "%s" créé (id %d).', $user->getEmail(), $user->getId()));

        return Command::SUCCESS;
    }

    private function runOnUser(string $action, ?string $identifier, InputInterface $input, SymfonyStyle $io): int
    {
        $user = $this->findUser($identifier, $input, $io);
        if (null === $user) {
            return Command::FAILURE;
        }

        return match ($action) {
            'show' => $this->showUser($user, $io),
            'update' => $this->updateUser($user, $input, $io),
            'delete' => $this->deleteUser($user, $input, $io),
        };
    }

    private function showUser(User $user, SymfonyStyle $io): int
    {
        $io->title(sprintf('Utilisateur #%d', $user->getId()));
        $io->definitionList(
            ['Email' => $user->getEmail()],
            ['Prénom' => $user->getFirstname() ?? '-'],
            ['Nom' => $user->getLastname() ?? '-'],
            ['Pseudo' => $user->getPseudo() ?? '-'],
            ['Pseudo affiché' => $user->isPseudoDisplayed() ? 'oui' : 'non'],
            ['Rôles' => implode(', ', $user->getRoles())],
            ['Vérifié' => $user->isVerified() ? 'oui' : 'non'],
            ['Livres' => $user->getBooks()->count()],
            ['Créé le' => $user->getCreatedAt()?->format('d/m/Y H:i') ?? '-'],
            ['Modifié le' => $user->getUpdatedAt()?->format('d/m/Y H:i') ?? '-'],
        );

        return Command::SUCCESS;
    }

    private function updateUser(User $user, InputInterface $input, SymfonyStyle $io): int
    {
        if (!$input->isInteractive()) {
            $io->error('L\'action "update" nécessite un terminal interactif.');

            return Command::FAILURE;
        }

        $io->title(sprintf('Mise à jour de "%s"', $user->getEmail()));
        $io->note('Laisse vide pour conserver la valeur actuelle.');

        $email = $io->ask('Email', $user->getEmail());
        if (null !== $email && '' !== trim($email)) {
            $email = trim($email);
            if (!filter_var($email, \FILTER_VALIDATE_EMAIL)) {
                $io->error(sprintf('"%s" n\'est pas une adresse email valide.', $email));

                return Command::FAILURE;
            }

            $existing = $this->userRepository->findOneBy(['email' => $email]);
            if (null !== $existing && $existing->getId() !== $user->getId()) {
                $io->error(sprintf('Un autre compte utilise déjà l\'adresse "%s".', $email));

                return Command::FAILURE;
            }

            $user->setEmail($email);
        }

        $user->setFirstname($io->ask('Prénom', $user->getFirstname()));
        $user->setLastname($io->ask('Nom', $user->getLastname()));
        $user->setPseudo($io->ask('Pseudo', $user->getPseudo()));
        $user->setRoles($this->askRoles($io, $user->getRoles()));
        $user->setIsPseudoDisplayed($io->confirm('Afficher le pseudo publiquement ?', (bool) $user->isPseudoDisplayed()));
        $user->setIsVerified($io->confirm('Compte vérifié ?', $user->isVerified()));

        $plainPassword = $this->askPassword($input, $io, 'Nouveau mot de passe (vide = inchangé)', required: false);
        if (null !== $plainPassword) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
        }

        $user->setUpdatedAt(new \DateTimeImmutable());
        $this->entityManager->flush();

        $io->success(sprintf('Utilisateur "%s" mis à jour.', $user->getEmail()));

        return Command::SUCCESS;
    }

    private function deleteUser(User $user, InputInterface $input, SymfonyStyle $io): int
    {
        $bookCount = $user->getBooks()->count();

        if (!$input->getOption('force')) {
            if (!$input->isInteractive()) {
                $io->error('Suppression refusée sans confirmation. Utilise --force pour forcer.');

                return Command::FAILURE;
            }

            $message = 0 === $bookCount
                ? sprintf('Supprimer définitivement "%s" ?', $user->getEmail())
                : sprintf('Supprimer définitivement "%s" ? Ses %d livre(s) seront détachés (conservés sans propriétaire).', $user->getEmail(), $bookCount);

            if (!$io->confirm($message, false)) {
                $io->warning('Suppression annulée.');

                return Command::SUCCESS;
            }
        }

        $email = $user->getEmail();

        foreach ($user->getBooks() as $book) {
            $user->removeBook($book);
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $io->success(sprintf('Utilisateur "%s" supprimé.', $email));

        return Command::SUCCESS;
    }

    private function showHelp(SymfonyStyle $io): int
    {
        $io->title('Aide de la commande '.$this->getName());
        $io->writeln($this->getProcessedHelp());
        $io->newLine();

        return Command::SUCCESS;
    }

    private function findUser(?string $identifier, InputInterface $input, SymfonyStyle $io): ?User
    {
        if (null !== $identifier && '' !== $identifier) {
            $user = ctype_digit($identifier)
                ? $this->userRepository->find((int) $identifier)
                : $this->userRepository->findOneBy(['email' => $identifier]);

            if (null === $user) {
                $io->error(sprintf('Aucun utilisateur trouvé pour "%s".', $identifier));
            }

            return $user;
        }

        if (!$input->isInteractive()) {
            $io->error('Précise l\'id ou l\'email de l\'utilisateur.');

            return null;
        }

        $users = $this->userRepository->findBy([], ['email' => 'ASC']);
        if ([] === $users) {
            $io->warning('Aucun utilisateur en base.');

            return null;
        }

        $emails = array_map(static fn (User $u): string => (string) $u->getEmail(), $users);
        $email = $io->choice('Quel utilisateur ?', $emails);

        return $this->userRepository->findOneBy(['email' => $email]);
    }

    private function askEmail(InputInterface $input, SymfonyStyle $io): ?string
    {
        $email = (string) $io->ask('Email', null, static function (?string $value): string {
            $value = trim((string) $value);
            if (!filter_var($value, \FILTER_VALIDATE_EMAIL)) {
                throw new \RuntimeException('Adresse email invalide.');
            }

            return $value;
        });

        return '' !== $email ? $email : null;
    }

    private function askPassword(InputInterface $input, SymfonyStyle $io, string $question, bool $required = true): ?string
    {
        return $io->askHidden($question, function (?string $value) use ($required): ?string {
            $value = (string) $value;

            if ('' === $value) {
                if ($required) {
                    throw new \RuntimeException('Le mot de passe ne peut pas être vide.');
                }

                return null;
            }

            if (\strlen($value) < 6) {
                throw new \RuntimeException('Le mot de passe doit contenir au moins 6 caractères.');
            }

            return $value;
        });
    }

    /**
     * @param list<string> $currentRoles
     *
     * @return list<string>
     */
    private function askRoles(SymfonyStyle $io, array $currentRoles): array
    {
        $answer = (string) $io->ask('Rôles (séparés par des virgules)', implode(',', $currentRoles));

        $roles = array_values(array_unique(array_filter(array_map(
            static fn (string $role): string => strtoupper(trim($role)),
            explode(',', $answer),
        ))));

        return [] === $roles ? ['ROLE_USER'] : $roles;
    }
}
