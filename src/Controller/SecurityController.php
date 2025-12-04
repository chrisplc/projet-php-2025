<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class SecurityController extends AbstractController
{
    #[Route('/security', name: 'app_security')]
    public function index(): Response
    {
        return $this->render('security/index.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        if ($request->isMethod('POST')) {
            $nom = $request->request->get('nom');
            $prenom = $request->request->get('prenom');
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $passwordConfirm = $request->request->get('password_confirm');

            // Vérifier que les mots de passe correspondent
            if ($password !== $passwordConfirm) {
                return $this->render('security/register.html.twig', [
                    'error' => 'Les mots de passe ne correspondent pas'
                ]);
            }

            // Vérifier si l'email existe déjà
            $existingUser = $entityManager->getRepository(Utilisateur::class)->find($email);
            if ($existingUser) {
                return $this->render('security/register.html.twig', [
                    'error' => 'Cet email est déjà utilisé'
                ]);
            }

            // Créer le nouvel utilisateur
            $utilisateur = new Utilisateur();
            $utilisateur->setEmail($email);
            $utilisateur->setNom($nom);
            $utilisateur->setPrenom($prenom);
            
            // Hasher le mot de passe
            $hashedPassword = $passwordHasher->hashPassword($utilisateur, $password);
            $utilisateur->setPassword($hashedPassword);

            // Enregistrer en base de données
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            // Rediriger vers la page de connexion
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig');
    }

    #[Route('/login', name: 'app_login')]
    public function login(): Response
    {
        return $this->render('security/login.html.twig');
    }
}
