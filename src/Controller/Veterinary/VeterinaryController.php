<?php

namespace App\Controller\Veterinary;

use App\Entity\Reports;
use App\Repository\UsersRepository;
use App\Repository\HabitatsRepository;
use App\Repository\ReportsRepository;
use App\Form\ComType;
use App\Entity\Users;
use App\Repository\ReportsVetRepository;
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

    // -----------------Ajout d'un rapport pour l'habitat-------------------------
    #[Route('/vet/comHab/add/{id}', name: 'vet_com_add')]
    public function add(Request $request, EntityManager $entityManager, $id): Response
    {
        // Récupérer l'habitat associé à l'ID
        $habitat = $this->habitatsRepository->find($id);

        if (!$habitat) {
            throw $this->createNotFoundException('Habitat non trouvé');
        }

        // Vérifier si l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Créer une nouvelle entité Report
        $report = new Reports();
        // Créer le formulaire avec le formulaire ComType (assurez-vous que ComType est bien défini)
        $form = $this->createForm(ComType::class, $report, [
            'user' => $user,
            'habitat' => $habitat,
        ]);

        // Traiter la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $report->setIdHabitats($habitat); // Associer l'habitat
            $report->setIdUsers($user); // Associer l'utilisateur
            $report->setDate(new \DateTime()); // Ajouter la date actuelle
            $report->setComment($form->get('comment')->getData());

            try {
                // Sauvegarder le rapport dans la base de données
                $entityManager->persist($report);
                $entityManager->flush();
                $this->addFlash('success', 'Rapport ajouté avec succès.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout du rapport.');
            }

            return $this->redirectToRoute('vet_com_list');
        }

        // Rendre la vue pour ajouter un rapport
        return $this->render('veterinary/comHab/add.html.twig', [
            'form' => $form->createView(),
            'habitat' => $habitat,
        ]);
    }

    // -----------------Liste des rapports-------------------------

    #[Route('/vet/comHab/list', name: 'vet_com_list')]
    public function listComments(Request $request, ReportsVetRepository $repo): Response
    {
        // Vérifier si l'utilisateur est employé ou vétérinaire
        if (!$this->isGranted('ROLE_EMPLOYEE') && !$this->isGranted('ROLE_VETERINARY')) {
            return $this->redirectToRoute('app_home');
        }

        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Récupérer l'habitat sélectionné depuis la requête
        $selectedHabitatId = $request->query->get('habitat');

        // Récupérer les rapports en fonction de l'habitat sélectionné
        // $reports = $this->reportsRepository->findCom($user, $selectedHabitatId);
        $id=1;
        $reports = $repo->find($id);
        dd($reports);

        // Rendre la vue avec la liste des rapports
        return $this->render('veterinary/comHab/list.html.twig', [
            'habitats' => $this->habitatsRepository->findAll(),
            'selectedHabitat' => $selectedHabitatId,
            'reports' => $reports,
        ]);
    }
}
