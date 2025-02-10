<?php

namespace App\Controller;

use App\Entity\Animals;
use App\Entity\Habitats;
use App\Entity\Reports;
use App\Entity\ReportsVet;
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
        $habitat = $this->entityManager->getRepository(Habitats::class)->find(2);
        
        // Si l'habitat n'est pas trouvé, afficher une erreur
        if (!$habitat) {
            throw $this->createNotFoundException('Habitat non trouvé');
        }

        // On récupère les animaux par habitat spécifique
        $maraisAnimals = $this->entityManager->getRepository(Animals::class)->findBy(['idHabitats' => 2]);

        $reportsByAnimal = [];
        $latestReportVet = $this->entityManager->getRepository(ReportsVet::class)->findOneBy(
        ['idHabitats' => 2],
        ['date' => 'DESC']
    );

    foreach ($maraisAnimals as $animal) {
        $reportsByAnimal[$animal->getId()] = $this->entityManager->getRepository(Reports::class)->findBy(['idAnimals' => $animal]);
    }


        // Envoie des données à la vue
        return $this->render('marais/index.html.twig', [
            'habitat' => $habitat,  // Passer l'objet habitat à la vue
            'maraisAnimals' => $maraisAnimals,
            'reportsByAnimal' => $reportsByAnimal,
            'pictureService' => $this->pictureService,
            'latestReport' => $latestReportVet,  // Assurez-vous que la variable 'latestReport' est bien utilisée dans la vue        ]);

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
    
        return $this->render('marais/index.html.twig', [
            'habitat' => $habitat,
            'latestReport' => $latestReportVet,  // Assurez-vous que la variable 'latestReport' est bien utilisée dans la vue
            'maraisAnimals' => $habitat->getAnimals(),
        ]);
    }    

}
