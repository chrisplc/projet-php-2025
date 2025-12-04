<?php

namespace App\Controller\Admin;

use App\Entity\Reservation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use Doctrine\ORM\EntityManagerInterface;

class ReservationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Reservation::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Gestion des réservations')
            ->setPageTitle('detail', 'Détails de la réservation')
            ->setEntityLabelInSingular('Réservation')
            ->setEntityLabelInPlural('Réservations')
            ->setDefaultSort(['dateReservation' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID')
                ->onlyOnIndex(),
            AssociationField::new('spectacle', 'Spectacle')
                ->setRequired(true),
            AssociationField::new('utilisateur', 'Client')
                ->setRequired(true),
            IntegerField::new('nombrePlaces', 'Nombre de places')
                ->setRequired(true),
            MoneyField::new('prixUnitaire', 'Prix unitaire')
                ->setCurrency('EUR')
                ->setRequired(true),
            MoneyField::new('prixTotal', 'Prix total')
                ->setCurrency('EUR')
                ->setRequired(true),
            TextField::new('dateReservationFormatted', 'Date de réservation')
                ->hideOnForm()
                ->setRequired(false),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        // Remettre les places disponibles
        $spectacle = $entityInstance->getSpectacle();
        $spectacle->setPlacesDisponibles(
            $spectacle->getPlacesDisponibles() + $entityInstance->getNombrePlaces()
        );
        
        $entityManager->persist($spectacle);
        $entityManager->remove($entityInstance);
        $entityManager->flush();
    }
}

