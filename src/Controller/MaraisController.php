<?php

namespace App\Controller;

use App\Entity\Animals;
use App\Repository\HabitatsRepository;
use App\Repository\ReportsVetRepository;
use App\Service\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MaraisController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private PictureService $pictureService;

    // Injection de l'EntityManager et du service PictureService dans le constructeur
    public function __construct(EntityManagerInterface $entityManager, PictureService $pictureService)
    {
        $this->entityManager = $entityManager;
        $this->pictureService = $pictureService;
    }

    #[Route('/marais', name: 'app_marais')]
    public function index(): Response
    {
        // On récupère les animaux par habitat spécifique
        $jungleAnimals = $this->entityManager->getRepository(Animals::class)->findBy(['idHabitats' => 1]);
        $maraisAnimals = $this->entityManager->getRepository(Animals::class)->findBy(['idHabitats' => 2]);
        $savaneAnimals = $this->entityManager->getRepository(Animals::class)->findBy(['idHabitats' => 3]);

        // Envoie des données à la vue
        return $this->render('marais/index.html.twig', [
            'jungleAnimals' => $jungleAnimals,
            'maraisAnimals' => $maraisAnimals,
            'savaneAnimals' => $savaneAnimals,
            'pictureService' => $this->pictureService,
        ]);
    }

    public function habitatDetails(int $id, ReportsVetRepository $reportsVetRepo, HabitatsRepository $habitatsRepo): Response
    {
        $habitat = $habitatsRepo->find($id);
        $latestReport = $reportsVetRepo->findOneBy(['idHabitats' => $habitat], ['date' => 'DESC']);

        return $this->render('habitat/details.html.twig', [
            'habitat' => $habitat,
            'latestReport' => $latestReport,
            'jungleAnimals' => $habitat->getAnimals(), // Supposons que la relation soit déjà définie
        ]);
    }

}
