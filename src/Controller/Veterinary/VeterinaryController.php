<?php

namespace App\Controller\Veterinary;

use App\Entity\Reports;
use App\Repository\UsersRepository;
use App\Repository\HabitatsRepository;
use App\Repository\ReportsRepository;
use App\Form\ComType;
use App\Entity\Users;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VeterinaryController extends AbstractController
{
    private $habitatsRepository;
    private $reportsRepository;
    private $usersRepository;

    // Injection des services nécessaires
    public function __construct(HabitatsRepository $habitatsRepository, ReportsRepository $reportsRepository, UsersRepository $usersRepository)
    {
        $this->habitatsRepository = $habitatsRepository;
        $this->reportsRepository = $reportsRepository;
        $this->usersRepository = $usersRepository;
    }

    // -----------------Route pour l'index vétérinaire-------------------------
    #[Route('/veterinary', name: 'app_veterinary')]
public function index(Request $request): Response
{
    // Vérifier que l'utilisateur est vétérinaire
    if (!$this->isGranted('ROLE_VETERINARY')) {
        return $this->redirectToRoute('app_home');
    }

    // Récupérer l'utilisateur connecté
    $user = $this->getUser();

    // Vérifier si un utilisateur est connecté
    if (!$user) {
        return $this->redirectToRoute('app_login');  // Rediriger si l'utilisateur n'est pas connecté
    }

    // Récupérer le nom de l'utilisateur
    $username = $user ? $user->getFirstName() . ' ' . $user->getName() : 'Invité';
    
    // Récupérer tous les habitats pour la sélection
    $habitats = $this->habitatsRepository->findAll();

    // Récupérer l'habitat sélectionné à partir de la requête
    $selectedHabitatId = $request->query->get('habitat');
    $selectedHabitat = $selectedHabitatId ? $this->habitatsRepository->find($selectedHabitatId) : null;

    // Récupérer les rapports en fonction de l'habitat sélectionné
    if ($selectedHabitat) {
        $reports = $this->reportsRepository->findByHabitat($selectedHabitat);
    } else {
        $reports = $this->reportsRepository->findAll();
    }

    // Rendre la vue avec tous les éléments nécessaires
    return $this->render('veterinary/index.html.twig', [
        'user' => $user,
        'username' => $username,
        'habitats' => $habitats,
        'selectedHabitat' => $selectedHabitat,
        'reports' => $reports,
    ]);
}

}
