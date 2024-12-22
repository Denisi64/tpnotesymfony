<?php
// src/Controller/TopicController.php

namespace App\Controller;

use App\Repository\TopicRepository;
use App\Repository\ReplyRepository; // Le repository des réponses (Reply)
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TopicController extends AbstractController
{
    #[Route('/topic/{id}', name: 'app_topic_show')]
    public function show(int $id, TopicRepository $topicRepository, ReplyRepository $replyRepository): Response
    {
        // Récupère le topic avec l'id spécifié
        $topic = $topicRepository->find($id);

        if (!$topic) {
            throw $this->createNotFoundException('Topic not found');
        }

        // Récupère toutes les réponses (replies) liées à ce topic
        $replies = $replyRepository->findBy(['topic' => $topic]);

        // Passe les données (topic et replies) à la vue Twig
        return $this->render('topic/show.html.twig', [
            'topic' => $topic,
            'replies' => $replies,
        ]);
    }
}

