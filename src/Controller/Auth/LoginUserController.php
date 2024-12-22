<?php
// src/Controller/Auth/AuthController.php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginUserController extends AbstractController
{
    private $userRepository;
    private $entityManager;
    private $mailer;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager, MailerInterface $mailer)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
    }

    // Route pour la page de login
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    // Route pour la page d'enregistrement
    #[Route('/register', name: 'page_register')]
    public function register(): Response
    {
        return $this->render('security/register.html.twig');
    }

    // Route pour la page "mot de passe oublié"
    #[Route(path: '/forgot-password', name: 'page_forgot_password', methods: ['GET', 'POST'])]
    public function forgotPassword(Request $request): Response
    {
        // Traitement du formulaire
        if ($request->isMethod('POST')) {
            $email = $request->get('email');
            $user = $this->userRepository->findOneBy(['email' => $email]);

            if (!$user) {
                $this->addFlash('error', 'Aucun utilisateur trouvé avec cette adresse email.');
                return $this->redirectToRoute('page_forgot_password');
            }

            // Générer un token unique
            $resetToken = Uuid::v4();
            $user->setResetToken($resetToken);
            $this->entityManager->flush(); // Utilisation de l'EntityManager injecté

            // Créer l'URL pour réinitialiser le mot de passe
// Créer l'URL pour réinitialiser le mot de passe
            $resetUrl = $this->generateUrl(
                'page_reset_password',
                ['token' => $resetToken],
                \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL
            );

            // Envoi de l'email avec le lien
            $email = (new Email())
                ->from('no-reply@yourdomain.com')
                ->to($user->getEmail())
                ->subject('Réinitialisation du mot de passe')
                ->html('<p>Bonjour,</p><p>Vous avez demandé une réinitialisation de votre mot de passe. Cliquez sur le lien suivant pour réinitialiser votre mot de passe : <a href="' . $resetUrl . '">Réinitialiser le mot de passe</a></p>');

            $this->mailer->send($email);

            // Message flash et redirection
            $this->addFlash('success', 'Un email de réinitialisation a été envoyé.');
            return $this->redirectToRoute('page_forgot_password');
        }

        return $this->render('security/forgot_password.html.twig');
    }

    // Route pour la réinitialisation du mot de passe
    #[Route('/reset/{token}', name: 'page_reset_password')]
    public function resetPassword($token, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Vérifier que le token est valide
        $user = $this->userRepository->findOneBy(['resetToken' => $token]);

        if (!$user) {
            $this->addFlash('error', 'Token invalide ou expiré.');
            return $this->redirectToRoute('page_forgot_password');
        }

        // Si la méthode est POST, traiter le formulaire de réinitialisation
        if ($request->isMethod('POST')) {
            $password = $request->get('password');
            $confirmPassword = $request->get('confirm_password');

            if ($password !== $confirmPassword) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
            } else {
                // Hasher le mot de passe et sauvegarder
                $hashedPassword = $passwordHasher->hashPassword($user, $password);
                $user->setPassword($hashedPassword);
                $user->setResetToken(null);  // Supprimer le token après réinitialisation

                // Sauvegarder les changements
                $this->entityManager->flush(); // Utilisation de l'EntityManager injecté

                $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès.');
                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('security/reset_password.html.twig', ['token' => $token]);
    }




    #[Route('/confirm', name: 'page_confirm_account')]
    public function confirmAccount(): Response
    {
        return $this->render('auth/confirm.html.twig');
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout()
    {
        // Cette méthode ne sera jamais exécutée, Symfony gère la déconnexion automatiquement.
    }
    #[Route('/test-email', name: 'test_email')]
    public function testEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('test@localhost')
            ->to('test@example.com')
            ->subject('Test Email')
            ->text('Ceci est un test.');

        $mailer->send($email);
        return new Response('Email envoyé');
    }

}
