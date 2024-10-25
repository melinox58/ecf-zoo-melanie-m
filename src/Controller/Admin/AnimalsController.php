<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AnimalsRepository;

class AnimalsController extends AbstractController
{
    #[Route('/admin/anim', name: 'app_admin_anim')]
    public function index(AnimalsRepository $animalsRepository): Response
    {
        $animals = $animalsRepository->findBy([], ['nameAnimal' =>
        'asc']);

        return $this->render('admin/animals/index.html.twig', compact
        ('animals'));
    }
}

