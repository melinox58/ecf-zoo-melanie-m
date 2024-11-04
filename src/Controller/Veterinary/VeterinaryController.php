<?php

namespace App\Controller\Veterinary;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class VeterinaryController extends AbstractController
{
    #[Route('/veterinary', name: 'app_veterinary')]
    public function index(): Response
    {
        if (!$this->isGranted('ROLE_VETERINARY')) {
            
            return $this->redirectToRoute('app_home');
        }

        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        
        // Récupérer le nom de l'utilisateur ou afficher "Invité" si l'utilisateur n'est pas connecté
        $username = $user ? $user->getFirstName() . ' ' . $user->getName() : 'Invité';

        // Rendre la vue pour l'administrateur
        return $this->render('veterinary/index.html.twig', [
            'user' => $user,
            'username' => $username,
        ]);
    }
}
