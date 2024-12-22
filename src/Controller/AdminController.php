<?php
// src/Controller/AdminController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends AbstractController
{
    #[Route("/admin", name: "admin_panel")]
    public function index(): Response
    {
        // Afficher un tableau de bord admin
        return $this->render('admin/index.html.twig');
    }
}
