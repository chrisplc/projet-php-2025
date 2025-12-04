<?php

namespace App\Command;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Crée un utilisateur administrateur',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = 'admin@test.com';
        $password = 'admin';

        // Vérifier si l'admin existe déjà
        $existingAdmin = $this->entityManager->getRepository(Utilisateur::class)->find($email);
        
        if ($existingAdmin) {
            // Mettre à jour l'admin existant
            $existingAdmin->setPassword($this->passwordHasher->hashPassword($existingAdmin, $password));
            $existingAdmin->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
            $io->success('Administrateur mis à jour avec succès !');
        } else {
            // Créer un nouvel admin
            $admin = new Utilisateur();
            $admin->setEmail($email);
            $admin->setNom('Admin');
            $admin->setPrenom('Administrateur');
            $admin->setPassword($this->passwordHasher->hashPassword($admin, $password));
            $admin->setRoles(['ROLE_ADMIN', 'ROLE_USER']);

            $this->entityManager->persist($admin);
            $io->success('Administrateur créé avec succès !');
        }

        $this->entityManager->flush();

        $io->note([
            'Email: ' . $email,
            'Mot de passe: ' . $password,
        ]);

        return Command::SUCCESS;
    }
}

