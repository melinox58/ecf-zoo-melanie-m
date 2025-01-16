<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Users;


class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        // Récupérer l'utilisateur connecté et vérifier qu'il est de type Users
        /** @var Users|null $user */
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
