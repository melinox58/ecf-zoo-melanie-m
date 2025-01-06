<?php

namespace App\Controller\Veterinary;

use App\Entity\Habitats;
use App\Entity\Reports;
use App\Entity\ReportsVet;
use App\Form\ComType;
use App\Entity\Users;
use App\Repository\HabitatsRepository;
use App\Repository\ReportsVetRepository;
use App\Repository\UsersRepository;
use App\Repository\ReportsRepository;
use App\Form\ReportsVetType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AnimalsRepository;

class ReportsVetController extends AbstractController
{
    private $habitatsRepository;
    private $reportsVetRepository;
    private $usersRepository;
    private $animalsRepository;
    private $reportsRepository;

     // Injection des services nécessaires
    public function __construct(
        HabitatsRepository $habitatsRepository,
        ReportsVetRepository $reportsVetRepository,
        UsersRepository $usersRepository,
        AnimalsRepository $animalsRepository,
        ReportsRepository $reportsRepository
    ) {
        $this->habitatsRepository = $habitatsRepository;
        $this->reportsVetRepository = $reportsVetRepository;
        $this->usersRepository = $usersRepository;
        $this->reportsRepository = $reportsRepository;
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
            return $this->redirectToRoute('app_login');
        }

        // Accès aux données de l'utilisateur
        $username = $user->getFirstName() . ' ' . $user->getName();  // Utilisation des getters ici

        return $this->render('veterinary/index.html.twig', [
            'user' => $user,
            'username' => $username,
        ]);
    }

    
    //Ajout d'un rapport animal
    // #[Route('/report/vet/new', name: 'app_report_vet_new')]
    // public function new(Request $request, EntityManagerInterface $entityManager): Response
    // {
    //     $reportVet = new ReportsVet();

    //     $user = $this->getUser(); // Assurez-vous d'obtenir l'utilisateur connecté

    //     if (!$user) {
    //         return $this->redirectToRoute('app_login');
    //     }

    //     // Associer l'utilisateur au rapport
    //     $reportVet->setIdUsers($user);

    //     // Créer et traiter le formulaire
    //     $form = $this->createForm(ReportsVetType::class, $reportVet, [
    //         'user' => $user,
    //         'habitats' => $this->habitatsRepository->findAll(),
    //     ]);

    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager->persist($reportVet);
    //         $entityManager->flush();

    //         // Ajouter un message flash de succès
    //         $this->addFlash('success', 'Le rapport a été créé avec succès.');

    //         return $this->redirectToRoute('vet_com_list');
    //     }

    //     return $this->render('veterinary/comHab/add.html.twig', [
    //         'form' => $form->createView(),
    //     ]);
    // }

    // filtre animaux

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

    //nouveau rapport animal

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
        $reports = $this->reportsVetRepository->findCom($user, $selectedHabitatId);
        $id=1;
        $reports = $repo->find($id);

        // Rendre la vue avec la liste des rapports
        return $this->render('veterinary/comHab/list.html.twig', [
            'habitats' => $this->habitatsRepository->findAll(),
            'selectedHabitat' => $selectedHabitatId,
            'reports' => $reports,
        ]);
    }

    // -----------------Ajout d'un rapport pour l'habitat-------------------------

    #[Route('/vet/comHab/add', name: 'vet_com_add')]
    public function addCom(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // Récupérer tous les habitats depuis la base de données
        $habitatsRepository = $entityManager->getRepository(Habitats::class);
        $habitats = $habitatsRepository->findAll();

        $report = new ReportsVet(); // Utiliser ReportsVet
        $form = $this->createForm(ReportsVetType::class, $report, [
            'user' => $user, // Passez l'objet utilisateur
            'habitats' => $habitats, // Passez les habitats sous forme de collection
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $habitats = $report->getIdHabitats();  // L'habitat sélectionné dans le formulaire
            $report->setIdUsers($user);  // Associer l'utilisateur actuel
            $report->setDate(new \DateTime());  // Ajouter la date actuelle

            try {
                $entityManager->persist($report);
                $entityManager->flush();
                $this->addFlash('success', 'Rapport ajouté avec succès.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout du rapport.');
            }

            return $this->redirectToRoute('vet_com_list');
        }

        return $this->render('veterinary/comHab/add.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}





