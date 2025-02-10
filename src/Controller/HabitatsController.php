<?php

namespace App\Controller;

use App\Entity\Animals;
use App\Entity\Habitats;
use App\Service\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HabitatsController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private PictureService $pictureService;

    // Injection de l'EntityManager et du service PictureService dans le constructeur
    public function __construct(EntityManagerInterface $entityManager, PictureService $pictureService)
    {
        $this->entityManager = $entityManager;
        $this->pictureService = $pictureService;
    }

    #[Route('/habitats', name: 'app_habitats')]
    public function index(): Response
    {
        // On récupère les animaux par habitat spécifique
        $habitats = $this->entityManager->getRepository(Habitats::class)->findAll();

        
        // Envoie des données à la vue
        return $this->render('habitats/index.html.twig', [
            'habitats' => $habitats,  // Passer l'objet habitat à la vue
            'pictureService' => $this->pictureService,
        ]);
        
    }
}
