<?php
// src/Controller/HomeController.php
namespace App\Controller;

use App\Repository\TopicRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(TopicRepository $topicRepository): Response
    {
        // Récupère tous les topics de la base de données
        $topics = $topicRepository->findAll();

        // Passe les topics à la vue Twig
        return $this->render('base.html.twig', [
            'title' => 'Bienvenue sur ma page d\'accueil',
            'topics' => $topics, // Liste des topics à afficher
        ]);
    }

    #[Route('/banned', name: 'app_banned')]
    public function banned(): Response
    {
        return $this->render('security/banned.html.twig', [
            'message' => 'Votre compte a été banni.',
        ]);
    }

}
