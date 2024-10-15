<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class VerinarinaryController extends AbstractController
{
    #[Route('/verinarinary', name: 'app_verinarinary')]
    public function index(): Response
    {
        return $this->render('verinarinary/index.html.twig', [
            'controller_name' => 'VerinarinaryController',
        ]);
    }
}
