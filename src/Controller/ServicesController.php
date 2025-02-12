<?php

namespace App\Controller;

use App\Entity\Services;
use App\Service\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ServicesController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private PictureService $pictureService;

    // Injection de l'EntityManager et du service PictureService dans le constructeur
    public function __construct(EntityManagerInterface $entityManager, PictureService $pictureService)
    {
        $this->entityManager = $entityManager;
        $this->pictureService = $pictureService;
    }

    #[Route('/services', name: 'app_services')]
    public function index(): Response
    {
        $service = $this->entityManager->getRepository(Services::class)->findAll();

        // Si le service n'est pas trouvé, afficher une erreur
        if (!$service) {
            throw $this->createNotFoundException('Service non trouvé');
        }

        // Assuming you want to process each service individually
        foreach ($service as $srv) {
            $srv->getName();
            
        }

        return $this->render('services/index.html.twig', [
            'pictureService' => $this->pictureService,
            'services' => $service,  
        ]);
    }
}
