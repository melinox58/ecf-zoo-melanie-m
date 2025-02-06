<?php

namespace App\Controller;

use App\Entity\Habitats;
use App\Entity\ReportsVet;
use App\Repository\HabitatsRepository;
use App\Repository\ReportsVetRepository;
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
        // On récupère l'habitat spécifique (par exemple, ID = 1 pour la jungle)
        $habitat = $this->entityManager->getRepository(Habitats::class)->find(1);
        
        // Si l'habitat n'est pas trouvé, afficher une erreur
        if (!$habitat) {
            throw $this->createNotFoundException('Habitat non trouvé');
        }

        // On récupère les animaux associés à cet habitat
        $jungleAnimals = $habitat->getAnimals();
        
        // Récupération des rapports vétérinaires pour les animaux de la jungle
        $reportsByAnimal = [];
        $latestReportVet = $this->entityManager->getRepository(ReportsVet::class)->findOneBy(
            ['idHabitats' => 1],
            ['date' => 'DESC']
        );
        
        foreach ($jungleAnimals as $animal) {
            $reportsByAnimal[$animal->getId()] = $this->entityManager->getRepository(Reports::class)->findBy(['idAnimals' => $animal]);
        }

        // Passer la variable 'habitat' à la vue
        return $this->render('jungle/index.html.twig', [
            'habitat' => $habitat,  // Passer l'objet habitat à la vue
            'jungleAnimals' => $jungleAnimals,
            'reportsByAnimal' => $reportsByAnimal,
            'pictureService' => $this->pictureService,
            'latestReport' => $latestReportVet,
        ]);
    }

    // Méthode pour récupérer les détails d'un habitat spécifique (optionnelle)
    public function habitatDetails(int $id, ReportsVetRepository $reportsVetRepo, HabitatsRepository $habitatsRepository)
    {
        // Récupérer l'habitat par ID
        $habitat = $habitatsRepository->find($id);
        
        // Si l'habitat n'est pas trouvé, afficher une erreur
        if (!$habitat) {
            throw $this->createNotFoundException('Habitat non trouvé');
        }

        // Récupérer le dernier rapport vétérinaire pour cet habitat
        $latestReportVet = $reportsVetRepo->findLatestReportVetByHabitat($id);

        return $this->render('jungle/index.html.twig', [
            'habitat' => $habitat,  // Passer l'habitat à la vue
            'latestReport' => $latestReportVet,
            'jungleAnimals' => $habitat->getAnimals(),  // Liste des animaux dans cet habitat
            'images' => $habitat->getImages(),  // Liste des images associées à l'habitat
        ]);
    }
}
