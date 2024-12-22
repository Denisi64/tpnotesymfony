<?php
// src/Repository/ReplyRepository.php

namespace App\Repository;

use App\Entity\Reply;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReplyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reply::class);
    }

    // Fonction pour récupérer les réponses d'un topic
    public function findByTopic($topic)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.topic = :topic')
            ->setParameter('topic', $topic)
            ->orderBy('r.createdAt', 'ASC') // Trie par date de création
            ->getQuery()
            ->getResult();
    }
}

