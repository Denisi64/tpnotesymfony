<?php
// src/EventListener/UserPasswordListener.php
namespace App\EventListener;

use App\Entity\User;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPasswordListener
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function prePersist(LifecycleEventArgs $event): void
    {
        $user = $event->getObject();  // Utilisez getObject() pour récupérer l'entité

        if ($user instanceof User && $user->getPassword()) {
            // Hacher le mot de passe avant de persister l'entité
            $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
        }
    }
}
