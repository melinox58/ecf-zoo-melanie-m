<?php

namespace App\Controller\Employe;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Users; // Assure-toi que cette ligne est présente

class EmployeeController extends AbstractController
{
    #[Route('/employee', name: 'app_employee')]
    public function index(): Response
    {
        // Récupérer l'utilisateur connecté
        /** @var Users|null $user */
        $user = $this->getUser();
        
        // Récupérer le nom de l'utilisateur ou afficher "Invité" si l'utilisateur n'est pas connecté
        $username = $user ? $user->getFirstName() . ' ' . $user->getName() : 'Invité';

        return $this->render('employee/index.html.twig', [
            'user' => $user,
            'username' => $username,
        ]);
    }
}
