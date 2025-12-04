<?php

namespace App\Command;

use App\Entity\Spectacle;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:generate-fixtures',
    description: 'Génère des données de test avec FakerPHP',
)]
class GenerateFixturesCommand extends Command
{
    private Generator $faker;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
        // Créer une instance de Faker avec la locale française
        $this->faker = Factory::create('fr_FR');
    }

    protected function configure(): void
    {
        $this
            ->addOption('users', null, InputOption::VALUE_OPTIONAL, 'Nombre d\'utilisateurs à générer', 10)
            ->addOption('spectacles', null, InputOption::VALUE_OPTIONAL, 'Nombre de spectacles à générer', 6)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $nbUsers = (int) $input->getOption('users');
        $nbSpectacles = (int) $input->getOption('spectacles');

        $io->title('Génération de données de test avec FakerPHP');

        // Générer des utilisateurs
        $io->section('Génération des utilisateurs');
        $progressBar = $io->createProgressBar($nbUsers);
        $progressBar->start();

        $created = 0;
        $skipped = 0;

        for ($i = 0; $i < $nbUsers; $i++) {
            // Utiliser Faker pour générer un email unique (nécessaire car c'est la clé primaire)
            $email = $this->faker->unique()->email();
            
            // Vérifier si l'email existe déjà
            $existingUser = $this->entityManager->getRepository(Utilisateur::class)->find($email);
            if ($existingUser) {
                $skipped++;
                $progressBar->advance();
                continue;
            }

            $utilisateur = new Utilisateur();
            $utilisateur->setEmail($email);
            // Générer nom et prénom avec Faker
            $utilisateur->setNom($this->faker->lastName());
            $utilisateur->setPrenom($this->faker->firstName());
            
            // Générer un mot de passe aléatoire avec Faker
            $password = $this->faker->password(8, 16);
            $hashedPassword = $this->passwordHasher->hashPassword($utilisateur, $password);
            $utilisateur->setPassword($hashedPassword);

            $this->entityManager->persist($utilisateur);
            $created++;
            $progressBar->advance();
        }

        $this->entityManager->flush();
        $progressBar->finish();
        $io->newLine(2);

        $io->success(sprintf(
            'Génération terminée ! %d utilisateur(s) créé(s), %d ignoré(s) (déjà existants)',
            $created,
            $skipped
        ));

        if ($created > 0) {
            $io->note('Les mots de passe sont générés aléatoirement pour chaque utilisateur.');
        }

        // Générer des spectacles
        $io->section('Génération des spectacles');
        $progressBar = $io->createProgressBar($nbSpectacles);
        $progressBar->start();

        $spectaclesData = [
            ['titre' => 'Le Roi Lion', 'prix' => '35.00', 'lieu' => 'Théâtre Mogador', 'image' => 'https://images.unsplash.com/photo-1503095396549-807759245b35?w=800&h=600&fit=crop', 'places' => 12],
            ['titre' => 'Mamma Mia!', 'prix' => '42.00', 'lieu' => 'Théâtre de Paris', 'image' => 'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?w=800&h=600&fit=crop', 'places' => 15],
            ['titre' => 'Les Misérables', 'prix' => '28.00', 'lieu' => 'Opéra Bastille', 'image' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?w=800&h=600&fit=crop', 'places' => 8],
            ['titre' => 'Starmania', 'prix' => '38.00', 'lieu' => 'Palais des Congrès', 'image' => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=800&h=600&fit=crop', 'places' => 20],
            ['titre' => 'Notre-Dame de Paris', 'prix' => '32.00', 'lieu' => 'Palais des Sports', 'image' => 'https://images.unsplash.com/photo-1478147427282-58a87a120781?w=800&h=600&fit=crop', 'places' => 18],
            ['titre' => 'Cats', 'prix' => '29.00', 'lieu' => 'Théâtre du Châtelet', 'image' => 'https://images.unsplash.com/photo-1508700115892-45ecd05ae2ad?w=800&h=600&fit=crop', 'places' => 6],
        ];

        $spectaclesCreated = 0;
        for ($i = 0; $i < min($nbSpectacles, count($spectaclesData)); $i++) {
            $data = $spectaclesData[$i];
            
            $spectacle = new Spectacle();
            $spectacle->setTitre($data['titre']);
            $spectacle->setPrix($data['prix']);
            $spectacle->setLieu($data['lieu']);
            $spectacle->setImage($data['image']);
            $spectacle->setPlacesDisponibles($data['places']);

            $this->entityManager->persist($spectacle);
            $spectaclesCreated++;
            $progressBar->advance();
        }

        $this->entityManager->flush();
        $progressBar->finish();
        $io->newLine(2);

        if ($spectaclesCreated > 0) {
            $io->success(sprintf('%d spectacle(s) créé(s)', $spectaclesCreated));
        }

        return Command::SUCCESS;
    }
}
