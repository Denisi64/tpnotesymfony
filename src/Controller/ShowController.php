<?php

namespace App\Controller;

use App\Entity\Reply;
use App\Entity\Topic;
use App\Form\ReplyTypeTopic;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/show/{id}', name: 'app_topic_show')]
    public function show(Topic $topic, Request $request): Response
    {
        // Créer une nouvelle instance de Reply
        $reply = new Reply();
        $replyForm = $this->createForm(ReplyTypeTopic::class, $reply);

        // Gérer la soumission du formulaire
        $replyForm->handleRequest($request);

        if ($replyForm->isSubmitted() && $replyForm->isValid()) {
            // Définir les valeurs pour la réponse
            $reply->setTopic($topic);
            $reply->setCreatedAt(new \DateTime());
            $reply->setAuthor($this->getUser()); // Assurer que l'utilisateur est connecté

            // Sauvegarder la réponse dans la base de données
            $this->entityManager->persist($reply);
            $this->entityManager->flush();

            // Rediriger vers la page du topic après la réponse
            return $this->redirectToRoute('app_topic_show', ['id' => $topic->getId()]);
        }

        // Passer le formulaire et le topic à la vue
        return $this->render('topic/show.html.twig', [
            'topic' => $topic,
            'replyForm' => $replyForm->createView(),
        ]);
    }
}
