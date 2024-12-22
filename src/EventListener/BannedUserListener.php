<?php
namespace App\EventListener;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;

class BannedUserListener
{
    private Security $security;
    private RouterInterface $router;

    public function __construct(Security $security, RouterInterface $router)
    {
        $this->security = $security;
        $this->router = $router;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $user = $this->security->getUser();

        // Vérifie si l'utilisateur est connecté et s'il est banni
        if ($user && in_array('ROLE_BANNED', $user->getRoles(), true)) {
            $currentRoute = $event->getRequest()->getPathInfo();

            // Évite une boucle infinie si l'utilisateur est déjà sur /banned
            if ($currentRoute !== '/banned') {
                $event->setResponse(new RedirectResponse($this->router->generate('app_banned')));
            }
        }
    }
}
