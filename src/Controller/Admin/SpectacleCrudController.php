<?php

namespace App\Controller\Admin;

use App\Entity\Spectacle;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class SpectacleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Spectacle::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Gestion des spectacles')
            ->setPageTitle('new', 'Créer un spectacle')
            ->setPageTitle('edit', 'Modifier un spectacle')
            ->setEntityLabelInSingular('Spectacle')
            ->setEntityLabelInPlural('Spectacles');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID')
                ->onlyOnIndex(),
            TextField::new('titre', 'Titre')
                ->setRequired(true),
            MoneyField::new('prix', 'Prix')
                ->setCurrency('EUR')
                ->setRequired(true),
            TextField::new('lieu', 'Lieu')
                ->setRequired(true),
            UrlField::new('image', 'URL de l\'image')
                ->setRequired(false),
            IntegerField::new('placesDisponibles', 'Places disponibles')
                ->setRequired(true)
                ->setHelp('Nombre de places disponibles pour ce spectacle'),
        ];
    }
}

