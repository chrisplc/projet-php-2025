<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Spectacle;
use App\Entity\Utilisateur;
use App\Repository\SpectacleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/reservation')]
class ReservationController extends AbstractController
{
    #[Route('/{id}', name: 'app_reservation', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function reserver(
        Spectacle $spectacle,
        Request $request,
        EntityManagerInterface $entityManager,
        SpectacleRepository $spectacleRepository
    ): Response {
        /** @var Utilisateur $user */
        $user = $this->getUser();

        if ($request->isMethod('POST')) {
            $nombrePlaces = (int) $request->request->get('nombre_places', 1);

            // Validation
            if ($nombrePlaces < 1) {
                $this->addFlash('error', 'Le nombre de places doit être au moins 1.');
                return $this->redirectToRoute('app_reservation', ['id' => $spectacle->getId()]);
            }

            if ($nombrePlaces > $spectacle->getPlacesDisponibles()) {
                $this->addFlash('error', 'Il n\'y a pas assez de places disponibles.');
                return $this->redirectToRoute('app_reservation', ['id' => $spectacle->getId()]);
            }

            // Calcul du prix (prix fixe par place)
            $prixUnitaire = (float) $spectacle->getPrix();
            $prixTotal = $prixUnitaire * $nombrePlaces;

            // Créer la réservation
            $reservation = new Reservation();
            $reservation->setUtilisateur($user);
            $reservation->setSpectacle($spectacle);
            $reservation->setNombrePlaces($nombrePlaces);
            $reservation->setPrixUnitaire((string) $prixUnitaire);
            $reservation->setPrixTotal((string) round($prixTotal, 2));
            $reservation->setDateReservation(new \DateTime());

            // Mettre à jour les places disponibles
            $spectacle->setPlacesDisponibles($spectacle->getPlacesDisponibles() - $nombrePlaces);

            $entityManager->persist($reservation);
            $entityManager->flush();

            // Rediriger vers la page de confirmation
            return $this->redirectToRoute('app_reservation_confirmation', ['id' => $reservation->getId()]);
        }

        // Récupérer tous les spectacles pour le sélecteur
        $tousLesSpectacles = $spectacleRepository->findAll();

        return $this->render('reservation/reserver.html.twig', [
            'spectacle' => $spectacle,
            'tousLesSpectacles' => $tousLesSpectacles,
        ]);
    }

    #[Route('/confirmation/{id}', name: 'app_reservation_confirmation')]
    #[IsGranted('ROLE_USER')]
    public function confirmation(Reservation $reservation): Response
    {
        /** @var Utilisateur $user */
        $user = $this->getUser();

        // Vérifier que la réservation appartient à l'utilisateur connecté
        if ($reservation->getUtilisateur()->getEmail() !== $user->getEmail()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette réservation.');
        }

        return $this->render('reservation/confirmation.html.twig', [
            'reservation' => $reservation,
        ]);
    }
}

