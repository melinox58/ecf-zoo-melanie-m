<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\Users;

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

        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        
        // Récupérer le nom de l'utilisateur ou afficher "Invité" si l'utilisateur n'est pas connecté
        $username = $user ? $user->getFirstName() . ' ' . $user->getName() : 'Invité';

        // Rendre la vue pour l'administrateur
        return $this->render('admin/index.html.twig', [
            'user' => $user,
            'username' => $username,
        ]);
    }
}
