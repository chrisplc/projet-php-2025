<?php

namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UtilisateurCrudController extends AbstractCrudController
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Utilisateur::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Gestion des utilisateurs')
            ->setPageTitle('new', 'Créer un utilisateur')
            ->setPageTitle('edit', 'Modifier un utilisateur')
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            EmailField::new('email', 'Email')
                ->setRequired(true),
            TextField::new('nom', 'Nom')
                ->setRequired(true),
            TextField::new('prenom', 'Prénom')
                ->setRequired(true),
            TextField::new('password', 'Mot de passe')
                ->setRequired(false)
                ->hideOnIndex()
                ->hideOnDetail()
                ->setHelp('Laissez vide pour ne pas modifier le mot de passe. Obligatoire lors de la création.'),
            ArrayField::new('roles', 'Rôles')
                ->setHelp('ROLE_USER est automatiquement ajouté'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->displayIf(function (Utilisateur $utilisateur) {
                    return $utilisateur->getEmail() !== $this->getUser()->getUserIdentifier();
                });
            });
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var Utilisateur $entityInstance */
        if (!$entityInstance->getPassword()) {
            throw new \RuntimeException('Le mot de passe est obligatoire lors de la création.');
        }
        
        if (!str_starts_with($entityInstance->getPassword(), '$')) {
            $entityInstance->setPassword(
                $this->passwordHasher->hashPassword($entityInstance, $entityInstance->getPassword())
            );
        }
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var Utilisateur $entityInstance */
        $plainPassword = $entityInstance->getPassword();
        
        // Si un nouveau mot de passe est fourni, le hasher
        if ($plainPassword && !str_starts_with($plainPassword, '$')) {
            $entityInstance->setPassword(
                $this->passwordHasher->hashPassword($entityInstance, $plainPassword)
            );
        } else {
            // Sinon, récupérer le mot de passe existant depuis la base de données
            $existingUser = $entityManager->getRepository(Utilisateur::class)->find($entityInstance->getEmail());
            if ($existingUser) {
                $entityInstance->setPassword($existingUser->getPassword());
            }
        }
        parent::updateEntity($entityManager, $entityInstance);
    }
}
