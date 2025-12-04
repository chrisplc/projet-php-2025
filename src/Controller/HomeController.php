<?php

namespace App\Controller;

use App\Repository\SpectacleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(SpectacleRepository $spectacleRepository): Response
    {
        $spectacles = $spectacleRepository->findAll();

        return $this->render('home/index.html.twig', [
            'spectacles' => $spectacles,
        ]);
    }
}
