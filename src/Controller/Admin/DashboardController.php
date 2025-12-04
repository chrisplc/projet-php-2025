<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use App\Entity\Utilisateur;
use App\Entity\Spectacle;
use App\Entity\Reservation;
use App\Repository\UtilisateurRepository;
use App\Repository\SpectacleRepository;
use App\Repository\ReservationRepository;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private UtilisateurRepository $utilisateurRepository,
        private SpectacleRepository $spectacleRepository,
        private ReservationRepository $reservationRepository
    ) {
    }

    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        // Statistiques
        $nbUtilisateurs = $this->utilisateurRepository->count([]);
        $nbSpectacles = $this->spectacleRepository->count([]);
        $nbReservations = $this->reservationRepository->count([]);
        
        // Réservations récentes
        $reservationsRecentes = $this->reservationRepository->findBy(
            [],
            ['dateReservation' => 'DESC'],
            5
        );

        // Spectacles avec peu de places
        $spectaclesFaibleStock = $this->spectacleRepository->createQueryBuilder('s')
            ->where('s.placesDisponibles < 10')
            ->orderBy('s.placesDisponibles', 'ASC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        return $this->render('admin/dashboard.html.twig', [
            'nbUtilisateurs' => $nbUtilisateurs,
            'nbSpectacles' => $nbSpectacles,
            'nbReservations' => $nbReservations,
            'reservationsRecentes' => $reservationsRecentes,
            'spectaclesFaibleStock' => $spectaclesFaibleStock,
            'urlUtilisateurs' => $adminUrlGenerator->setController('App\\Controller\\Admin\\UtilisateurCrudController')->setAction('index')->generateUrl(),
            'urlUtilisateursNew' => $adminUrlGenerator->setController('App\\Controller\\Admin\\UtilisateurCrudController')->setAction('new')->generateUrl(),
            'urlSpectacles' => $adminUrlGenerator->setController('App\\Controller\\Admin\\SpectacleCrudController')->setAction('index')->generateUrl(),
            'urlSpectaclesNew' => $adminUrlGenerator->setController('App\\Controller\\Admin\\SpectacleCrudController')->setAction('new')->generateUrl(),
            'urlReservations' => $adminUrlGenerator->setController('App\\Controller\\Admin\\ReservationCrudController')->setAction('index')->generateUrl(),
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Spektacles - Administration')
            ->setFaviconPath('favicon.ico')
            ->setTranslationDomain('admin');
    }

    #[Route('/statistiques', name: 'admin_statistiques')]
    public function statistiques(): Response
    {
        // Récupérer tous les spectacles
        $spectacles = $this->spectacleRepository->findAll();
        
        // Calculer les statistiques pour chaque spectacle
        $statistiques = [];
        foreach ($spectacles as $spectacle) {
            $reservations = $this->reservationRepository->findBy(['spectacle' => $spectacle]);
            
            $nombrePlacesReservees = 0;
            $prixTotal = 0;
            
            foreach ($reservations as $reservation) {
                $nombrePlacesReservees += $reservation->getNombrePlaces();
                $prixTotal += (float) $reservation->getPrixTotal();
            }
            
            $statistiques[] = [
                'spectacle' => $spectacle,
                'nombrePlacesReservees' => $nombrePlacesReservees,
                'prixTotal' => $prixTotal,
            ];
        }
        
        // Trier par prix total décroissant
        usort($statistiques, function($a, $b) {
            return $b['prixTotal'] <=> $a['prixTotal'];
        });
        
        // Calculer les totaux
        $totalPlacesReservees = array_sum(array_column($statistiques, 'nombrePlacesReservees'));
        $totalPrix = array_sum(array_column($statistiques, 'prixTotal'));
        
        // Top 5 par places réservées
        $top5Places = $statistiques;
        usort($top5Places, function($a, $b) {
            return $b['nombrePlacesReservees'] <=> $a['nombrePlacesReservees'];
        });
        $top5Places = array_slice($top5Places, 0, 5);
        
        // Top 5 par chiffre d'affaires (déjà trié par prix total)
        $top5CA = array_slice($statistiques, 0, 5);
        
        return $this->render('admin/statistiques.html.twig', [
            'statistiques' => $statistiques,
            'totalPlacesReservees' => $totalPlacesReservees,
            'totalPrix' => $totalPrix,
            'top5Places' => $top5Places,
            'top5CA' => $top5CA,
        ]);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        
        yield MenuItem::section('Gestion');
        
        yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-users', Utilisateur::class);
        yield MenuItem::linkToCrud('Spectacles', 'fa fa-theater-masks', Spectacle::class);
        yield MenuItem::linkToCrud('Réservations', 'fa fa-ticket-alt', Reservation::class);
        
        yield MenuItem::section('Rapports');
        yield MenuItem::linkToRoute('Statistiques', 'fa fa-chart-bar', 'admin_statistiques');
        
        yield MenuItem::section('Navigation');
        yield MenuItem::linkToRoute('Retour au site', 'fa fa-arrow-left', 'app_home');
        yield MenuItem::linkToLogout('Déconnexion', 'fa fa-sign-out-alt');
    }
}
