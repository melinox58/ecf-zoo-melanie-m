<?php

namespace App\Controller;

use App\Entity\Animals;
use App\Entity\Reports;
use App\Entity\ReportsVet;
use App\Repository\HabitatsRepository;
use App\Repository\ReportsVetRepository;
use App\Service\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SavaneController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private PictureService $pictureService;

    // Injection de l'EntityManager et du service PictureService dans le constructeur
    public function __construct(EntityManagerInterface $entityManager, PictureService $pictureService)
    {
        $this->entityManager = $entityManager;
        $this->pictureService = $pictureService;
    }

    #[Route('/savane', name: 'app_savane')]
    public function index(): Response
    {
        // On récupère les animaux par habitat spécifique
        $savaneAnimals = $this->entityManager->getRepository(Animals::class)->findBy(['idHabitats' => 3]);

        $reportsByAnimal = [];
        $latestReportVet = $this->entityManager->getRepository(ReportsVet::class)->findOneBy(
        ['idHabitats' => 3],
        ['date' => 'DESC']
    );
    
    foreach ($savaneAnimals as $animal) {
        $reportsByAnimal[$animal->getId()] = $this->entityManager->getRepository(Reports::class)->findBy(['idAnimals' => $animal]);
    }
        // Envoie des données à la vue
        return $this->render('savane/index.html.twig', [
            'savaneAnimals' => $savaneAnimals,
            'reportsByAnimal' => $reportsByAnimal,
            'pictureService' => $this->pictureService,
            'latestReport' => $latestReportVet,  // Assurez-vous que la variable 'latestReport' est bien utilisée dans la vue
        ]);
    }

    public function habitatDetails(
        int $id, 
        ReportsVetRepository $reportsVetRepo, 
        HabitatsRepository $habitatsRepo
    ): Response {
        $habitat = $habitatsRepo->find($id);
        // Dans la méthode habitatDetails
        $latestReportVet = $reportsVetRepo->findLatestReportVetByHabitat($id);
    
        return $this->render('jungle/index.html.twig', [
            'habitat' => $habitat,
            'latestReport' => $latestReportVet,  // Assurez-vous que la variable 'latestReport' est bien utilisée dans la vue
            'savaneAnimals' => $habitat->getAnimals(),
        ]);
    }    

}
