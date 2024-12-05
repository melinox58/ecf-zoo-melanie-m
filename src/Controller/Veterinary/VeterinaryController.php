<?php

namespace App\Controller\Veterinary;

use App\Entity\ReportsVet;
use App\Repository\HabitatsRepository;
use App\Repository\ReportsVetRepository;
use App\Repository\UsersRepository;
use App\Form\ReportsVetType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AnimalsRepository;

class VeterinaryController extends AbstractController
{
    private $habitatsRepository;
    private $reportsVetRepository;
    private $usersRepository;
    private $animalsRepository;

    public function __construct(
        HabitatsRepository $habitatsRepository,
        ReportsVetRepository $reportsVetRepository,
        UsersRepository $usersRepository,
        AnimalsRepository $animalsRepository
    ) {
        $this->habitatsRepository = $habitatsRepository;
        $this->reportsVetRepository = $reportsVetRepository;
        $this->usersRepository = $usersRepository;
    }

    #[Route('/veterinary', name: 'app_veterinary')]
    public function index(Request $request): Response
    {
        if (!$this->isGranted('ROLE_VETERINARY')) {
            return $this->redirectToRoute('app_home');
        }

        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Accès aux données de l'utilisateur
        $username = $user->getFirstName() . ' ' . $user->getName();  // Utilisation des getters ici

        $habitats = $this->habitatsRepository->findAll();

        $selectedHabitatId = $request->query->get('habitat');
        $selectedHabitat = $selectedHabitatId ? $this->habitatsRepository->find($selectedHabitatId) : null;

        if ($selectedHabitat) {
            $reports = $this->reportsVetRepository->findReportsVetByHabitat($selectedHabitat);
        } else {
            $reports = $this->reportsVetRepository->findAll();
        }

        return $this->render('veterinary/index.html.twig', [
            'user' => $user,
            'username' => $username,
            'habitats' => $habitats,
            'selectedHabitat' => $selectedHabitat,
            'reports' => $reports,

        
        ]);
    }

    #[Route('/report/vet/new', name: 'app_report_vet_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reportVet = new ReportsVet();

        $user = $this->getUser(); // Assurez-vous d'obtenir l'utilisateur connecté

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Associer l'utilisateur au rapport
        $reportVet->setIdUsers($user);

        // Créer et traiter le formulaire
        $form = $this->createForm(ReportsVetType::class, $reportVet, [
            'user' => $user,
            'habitats' => $this->habitatsRepository->findAll(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reportVet);
            $entityManager->flush();

            // Ajouter un message flash de succès
            $this->addFlash('success', 'Le rapport a été créé avec succès.');

            return $this->redirectToRoute('app_vet_reports_list');
        }

        return $this->render('veterinary/comHab/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/veterinary/comHab', name: 'app_vet_reports_list')]
    public function listReportsByUser(Request $request): Response
    {
        $user = $this->getUser();

        if (!$user || !$this->isGranted('ROLE_VETERINARY')) {
            return $this->redirectToRoute('app_home');
        }

        $reports = $this->reportsVetRepository->findReportsVetByUserId($user->getId());

        return $this->render('veterinary/comHab/list.html.twig', [
            'reports' => $reports,
            'user' => $user
        ]);
    }

    #[Route('/veterinary/comHab/delete/{id}', name: 'vet_comHab_delete', methods: ['POST'])]
    public function deleteOpinion(EntityManagerInterface $entityManager, int $id): Response
    {
        // Récupérer le rapport par ID
        $report = $this->reportsVetRepository->find($id);

        if (!$report) {
            // Si le rapport n'existe pas, rediriger avec un message d'erreur
            $this->addFlash('error', 'Le rapport n\'existe pas.');
            return $this->redirectToRoute('app_vet_reports_list');
        }

        // Supprimer le rapport
        $entityManager->remove($report);
        $entityManager->flush();

        // Ajouter un message flash de succès
        $this->addFlash('success', 'Le rapport a été supprimé avec succès.');

        return $this->redirectToRoute('app_vet_reports_list');
    }

    #[Route('/veterinary/reports', name: 'app_vet_anim')]
    public function report(Request $request, AnimalsRepository $animalsRepository): Response
    {
        // Créer un queryBuilder pour les animaux
        $queryBuilder = $animalsRepository->createQueryBuilder('a');

        // Récupérer les filtres depuis la requête
        $breedFilter = $request->query->get('breed');
        $nameFilter = $request->query->get('name');

        // Appliquer les filtres
        if ($breedFilter) {
            $queryBuilder->andWhere('a.breed = :breed')
                        ->setParameter('breed', $breedFilter);
        }

        if ($nameFilter) {
            $queryBuilder->andWhere('a.nameAnimal LIKE :name')
                        ->setParameter('name', '%' . $nameFilter . '%');
        }

        // Exécuter la requête et obtenir les résultats
        $animals = $queryBuilder->getQuery()->getResult();
        
        // Récupérer les races distinctes pour le filtre
        $races = $animalsRepository->createQueryBuilder('a')
            ->select('DISTINCT a.breed')
            ->getQuery()
            ->getResult();

        return $this->render('veterinary/reports/index.html.twig', [
            'animals' => $animals,
            'races' => $races,
        ]);
    }

    #[Route('/emp/report/add/{id}', name: 'vet_report_add')]
    public function add(Request $request, EntityManagerInterface $entityManager, UsersRepository $usersRepository, $id): Response
    {
        $report = new ReportsVet();

        // Récupérer l'animal par ID
        $animal = $this->animalsRepository->find($id);
        
        if (!$animal) {
            throw $this->createNotFoundException('Animal non trouvé');
        }

        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Vérifier si l'utilisateur est connecté
        if (!$user) {
            return $this->redirectToRoute('app_login'); // Rediriger vers la page de connexion
        }

        // Création du formulaire en passant l'utilisateur et l'animal
        $form = $this->createForm(ReportsVetType::class, $report, [
            'user' => $user,
            'animal' => $animal,
        ]);

        // Traitement de la requête
        $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();
        
        // Définir le poids et l'unité directement à partir du formulaire
        $report->setWeight($data->getWeight());
        $report->setUnit($data->getUnit());
        
        // Définir l'animal et l'utilisateur
        $report->setIdAnimals($animal);
        $report->setIdUsers($user);
        $report->setDate(new \DateTime());
        $report->setComment($data->getComment());

        // Persister le rapport
        $entityManager->persist($report);
        $entityManager->flush();
        
        return $this->redirectToRoute('app_emp_anim');

        //  // Ajouter un message flash
        //  $this->addFlash('success', 'Rapport enregistré avec succès.');
        }


            return $this->render('veterinary/reports/add.html.twig', [
                'form' => $form->createView(),
            ]);
        }
}
