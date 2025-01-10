<?php
namespace App\Controller;

use App\Entity\Animals;
use App\Entity\Reports;
use App\Repository\HabitatsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\MongoDBService;


class HomeController extends AbstractController
{
    private MongoDBService $mongoDBService;

    public function __construct(MongoDBService $mongoDBService)
    {
        $this->mongoDBService = $mongoDBService; // Stockez le service dans une propriété
    }
    
    #[Route('/', name: 'app_home')]
    public function index(HabitatsRepository $habitatsRepository, EntityManagerInterface $em): Response
    {
        // Récupérer les habitats
        $habitats = $habitatsRepository->findAllById();
        $topAnimals = $em->getRepository(Animals::class)->findBy([], ['views' => 'DESC'], 4);

        // Récupérer les avis et horaires comme avant
        $opinionsCollection = $this->mongoDBService->getCollection('opinions');
        $opinions = iterator_to_array($opinionsCollection->find(['isValidated' => true], ['sort' => ['date' => -1]]));
        
        $schedulesCollection = $this->mongoDBService->getCollection('schedules');
        $schedules = iterator_to_array($schedulesCollection->find());

        // Préparer un tableau associatif pour lier chaque animal à ses rapports
        $reportsByAnimal = [];
        foreach ($topAnimals as $animal) {
            $reportsByAnimal[$animal->getId()] = $em->getRepository(Reports::class)->findBy(['idAnimals' => $animal->getId()], ['date' => 'DESC']);
        }

        return $this->render('home/index.html.twig', [
            'opinions' => $opinions,
            'schedules' => $schedules,
            'habitats' => $habitats,  // Passez aussi les habitats à la vue
            'topAnimals' => $topAnimals,
            'reportsByAnimal' => $reportsByAnimal,
        ]);
    }
}
