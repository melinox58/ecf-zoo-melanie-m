<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\MongoDBService;
use MongoDB\BSON\ObjectId;

use function Symfony\Component\DependencyInjection\Loader\Configurator\iterator;

class HomeController extends AbstractController
{
    private MongoDBService $mongoDBService;

    public function __construct(MongoDBService $mongoDBService)
    {
        $this->mongoDBService = $mongoDBService; // Stockez le service dans une propriété
    }
    
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        // Récupération des avis validés, triés par date décroissante
        $opinionsCollection = $this->mongoDBService->getCollection('opinions');
        $opinions = iterator_to_array($opinionsCollection->find(['isValidated' => true], ['sort' => ['date' => -1]]));

        // Récupération des horaires (ajustez le filtre si besoin)
        $schedulesCollection = $this->mongoDBService->getCollection('schedules');
        $schedules = iterator_to_array($schedulesCollection->find());

        return $this->render('home/index.html.twig', [
            'opinions' => $opinions,
            'schedules' => $schedules,
        ]);
    }
}
