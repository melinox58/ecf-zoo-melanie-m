<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        // Vérifier si l'utilisateur a le rôle d'administrateur
        if (!$this->isGranted('ROLE_ADMIN')) {
            // Rediriger vers la page d'accueil si l'utilisateur n'est pas administrateur
            return $this->redirectToRoute('app_home');
        }

        // Rendre la vue pour l'administrateur
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}

