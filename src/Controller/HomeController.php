<?php
namespace App\Controller;

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
    public function index(): Response
    {
        // Utilisez $this->mongoDBService pour accéder au service
        $collection = $this->mongoDBService->getCollection('opinions');
        $opinions = $collection->find(); // Récupérer tous les avis

        return $this->render('home/index.html.twig', [
            'opinions' => $opinions,
        ]);
    }
}
