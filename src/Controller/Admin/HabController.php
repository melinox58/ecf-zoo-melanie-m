<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\HabitatsRepository;

class HabController extends AbstractController
{
    #[Route('/admin/hab', name: 'app_admin_hab')]
    public function index(HabitatsRepository $habitatsRepository): Response
    {
        $habitats = $habitatsRepository->findBy([], ['name' =>
        'asc']);

        return $this->render('admin/habitats/index.html.twig', compact
        ('habitats'));
    }

    #[Route('/edition/{id}', name: 'admin_habitats_edit')]
    public function edit(): Response
    {
        return $this->render('admin/habitats/modif.html.twig');
    }
}

