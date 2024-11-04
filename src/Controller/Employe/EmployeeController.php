<?php

namespace App\Controller\Employe;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EmployeeController extends AbstractController
{
    #[Route('/employee', name: 'app_employee')]
    public function index(): Response
    {
        // Vérifier si l'utilisateur est connecté et a le rôle d'employé
        if (!$this->isGranted('ROLE_EMPLOYEE')) {
            // Rediriger vers la page d'accueil si l'utilisateur n'est pas connecté ou n'a pas le rôle approprié
            return $this->redirectToRoute('app_home');
        }

        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        
        // Récupérer le nom de l'utilisateur ou afficher "Invité" si l'utilisateur n'est pas connecté
        $username = $user ? $user->getFirstName() . ' ' . $user->getName() : 'Invité';

        return $this->render('employee/index.html.twig', [
            'user' => $user,
            'username' => $username,
        ]);
    }
}
