<?php

namespace App\Controller;

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

        
        return $this->render('veterinary/index.html.twig', [
            'controller_name' => 'VeterinaryController',
        ]);
    }
}
