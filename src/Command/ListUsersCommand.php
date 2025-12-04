<?php

namespace App\Command;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:list-users',
    description: 'Liste tous les utilisateurs enregistrés dans la base de données',
)]
class ListUsersCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $users = $this->entityManager->getRepository(Utilisateur::class)->findAll();

        if (empty($users)) {
            $io->warning('Aucun utilisateur trouvé dans la base de données.');
            return Command::SUCCESS;
        }

        $io->title(sprintf('Liste des utilisateurs (%d)', count($users)));

        $rows = [];
        foreach ($users as $user) {
            $rows[] = [
                $user->getEmail(),
                $user->getNom(),
                $user->getPrenom(),
            ];
        }

        $io->table(
            ['Email', 'Nom', 'Prénom'],
            $rows
        );

        $io->success('Utilisateurs affichés avec succès !');

        return Command::SUCCESS;
    }
}
