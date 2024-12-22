<?php
// src/Controller/ProfileController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProfileController extends AbstractController
{
    #[Route("/profile", name: "app_profile")]
    public function index(): Response
    {
        // Vérifier si l'utilisateur est connecté
        $user = $this->getUser();

        if (!$user) {
            // Si l'utilisateur n'est pas connecté, redirige vers la page de connexion
            return $this->redirectToRoute('app_login');
        }

        // Si l'utilisateur est connecté, afficher son profil
        return $this->render('pages/profile.html.twig', [
            'user' => $user
        ]);
    }
}
