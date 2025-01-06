<?php

namespace App\Controller;

use App\Entity\Animals;
use App\Service\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Reports;

class JungleController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private PictureService $pictureService;

    // Injection de l'EntityManager et du service PictureService dans le constructeur
    public function __construct(EntityManagerInterface $entityManager, PictureService $pictureService)
    {
        $this->entityManager = $entityManager;
        $this->pictureService = $pictureService;
    }

    #[Route('/jungle', name: 'app_jungle')]
    public function index(): Response
    {
        // On récupère les animaux par habitat spécifique
        $jungleAnimals = $this->entityManager->getRepository(Animals::class)->findBy(['idHabitats' => 1]);

        // Récupération des rapports pour tous les animaux de la jungle
        $reports = $this->entityManager->getRepository(Reports::class)->findAll();

        // Envoie des données à la vue
        return $this->render('jungle/index.html.twig', [
            'jungleAnimals' => $jungleAnimals,
            'reports' => $reports,
            'pictureService' => $this->pictureService,
        ]);
    }
}
