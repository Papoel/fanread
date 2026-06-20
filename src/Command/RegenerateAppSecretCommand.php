<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'app:regenerate-app-secret',
    description: 'Génère un nouveau APP_SECRET et l\'écrit dans .env.local',
)]
class RegenerateAppSecretCommand extends Command
{
    private const ENV_KEY = 'APP_SECRET';

    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
        #[Autowire('%kernel.environment%')]
        private readonly string $environment,
        private readonly Filesystem $filesystem = new Filesystem(),
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ('prod' === $this->environment) {
            $io->error('Commande interdite en production. Régénérez le secret via votre gestionnaire de secrets / variables d\'environnement.');

            return Command::FAILURE;
        }

        $io->title('🔐 Régénération du APP_SECRET');

        $envLocalPath = $this->projectDir.'/.env.local';
        $newSecret = bin2hex(random_bytes(16));

        $io->section('Génération du secret');
        $io->definitionList(
            ['Fichier cible' => '.env.local'],
            ['Clé' => self::ENV_KEY],
            ['Nouveau secret' => $newSecret],
        );

        try {
            $previousSecret = $this->updateEnvFile($envLocalPath, $newSecret);
        } catch (\Throwable $e) {
            $io->error(sprintf('Impossible de mettre à jour le fichier : %s', $e->getMessage()));

            return Command::FAILURE;
        }

        if (null === $previousSecret) {
            $io->note(sprintf('La clé %s a été ajoutée à .env.local.', self::ENV_KEY));
        } else {
            $io->note(sprintf('Ancien secret remplacé : %s…', substr($previousSecret, 0, 8)));
        }

        $io->success('Le nouveau APP_SECRET a bien été enregistré dans .env.local.');
        $io->warning('Pensez à vider le cache : php bin/console cache:clear');

        return Command::SUCCESS;
    }

    /**
     * Met à jour (ou ajoute) la clé APP_SECRET dans le fichier .env.local.
     *
     * @return string|null L'ancienne valeur si elle existait, sinon null
     */
    private function updateEnvFile(string $path, string $newSecret): ?string
    {
        $content = $this->filesystem->exists($path) ? (string) file_get_contents($path) : '';
        $line = sprintf('%s=%s', self::ENV_KEY, $newSecret);
        $previous = null;

        if (1 === preg_match('/^'.self::ENV_KEY.'=(.*)$/m', $content, $matches)) {
            $previous = $matches[1];
            $content = (string) preg_replace('/^'.self::ENV_KEY.'=.*$/m', $line, $content);
        } else {
            $content = rtrim($content).\PHP_EOL.$line.\PHP_EOL;
            $content = ltrim($content, \PHP_EOL);
        }

        $this->filesystem->dumpFile($path, $content);

        return $previous;
    }
}
