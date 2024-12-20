<?php
// src/Controller/HomeController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        // Passe des variables à la vue Twig (par exemple un titre)
        return $this->render('base.html.twig', [
            'title' => 'Bienvenue sur ma page d\'accueil'
        ]);
    }
}
