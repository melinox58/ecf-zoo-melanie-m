<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ServicesRepository;

class ServController extends AbstractController
{
    #[Route('/admin/serv', name: 'app_admin_serv')]
    public function index(ServicesRepository $servicesRepository): Response
    {
        $services = $servicesRepository->findBy([], ['name' =>
        'asc']);

        return $this->render('admin/services/index.html.twig', compact
        ('services'));
    }

    #[Route('/edition/{id}', name: 'admin_services_edit')]
    public function edit(): Response
    {
        return $this->render('admin/services/modif.html.twig');
    }
}

