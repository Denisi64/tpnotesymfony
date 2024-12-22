<?php
// src/Controller/ReplyController.php

namespace App\Controller;

use App\Entity\Reply;
use App\Entity\Topic;
use App\Form\ReplyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ReplyTopicController extends AbstractController
{
    /**
     * @Route("/topic/{topicId}/reply/new", name="app_reply_new")
     */
    public function new(Request $request, EntityManagerInterface $em, $topicId, $parentId = null)
    {
        $topic = $em->getRepository(Topic::class)->find($topicId);
        if (!$topic) {
            throw $this->createNotFoundException('Topic not found');
        }

        $reply = new Reply();
        $reply->setTopic($topic);

        if ($parentId) {
            $parentReply = $em->getRepository(Reply::class)->find($parentId);
            if ($parentReply) {
                $reply->setParent($parentReply);
            }
        }

        $form = $this->createForm(ReplyType::class, $reply);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($reply);
            $em->flush();

            return $this->redirectToRoute('app_topic_show', ['id' => $topicId]);
        }

        return $this->render('reply/new.html.twig', [
            'form' => $form->createView(),
            'topic' => $topic,
        ]);
    }
}
