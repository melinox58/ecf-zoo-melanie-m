<?php

namespace App\Controller\Veterinary;

use App\Entity\Animals;
use App\Entity\Habitats;
use App\Entity\ReportsVet;
use App\Form\StatusAnimFormType;
use App\Repository\HabitatsRepository;
use App\Repository\ReportsVetRepository;
use App\Repository\UsersRepository;
use App\Repository\ReportsRepository;
use App\Form\ReportsVetType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AnimalsRepository;
use Symfony\Bundle\SecurityBundle\Security;

class ReportsVetController extends AbstractController
{
    private $habitatsRepository;
    private $reportsVetRepository;
    private $usersRepository;
    private $animalsRepository;
    private $reportsRepository;
    private $entityManager;

     // Injection des services nécessaires
    public function __construct(
        HabitatsRepository $habitatsRepository,
        ReportsVetRepository $reportsVetRepository,
        UsersRepository $usersRepository,
        AnimalsRepository $animalsRepository,
        ReportsRepository $reportsRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->habitatsRepository = $habitatsRepository;
        $this->reportsVetRepository = $reportsVetRepository;
        $this->usersRepository = $usersRepository;
        $this->reportsRepository = $reportsRepository;
        $this->entityManager = $entityManager;
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


    // -----------------Liste des rapports habitats-------------------------

    #[Route('/vet/comHab/list', name: 'vet_com_list')]
    public function listComments(
        Request $request, 
        ReportsVetRepository $repo, 
        HabitatsRepository $habitatsRepo
    ): Response {
        // Vérifie si l'utilisateur a les autorisations nécessaires
        if (!$this->isGranted('ROLE_EMPLOYEE') && !$this->isGranted('ROLE_VETERINARY')) {
            return $this->redirectToRoute('app_home');
        }

        $user = $this->getUser();
        $selectedHabitatId = $request->query->get('habitat');

        // Si un habitat est sélectionné, récupérer les rapports pour cet habitat
        if ($selectedHabitatId) {
            $reports = $repo->findCom($user, $selectedHabitatId);
        } else {
            // Si aucun habitat n'est sélectionné, récupérer tous les rapports pour l'utilisateur
            $reports = $repo->findAllForUser($user);
        }

        return $this->render('veterinary/comHab/list.html.twig', [
            'habitats' => $habitatsRepo->findAll(),
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

        // -----------------Status de l'animal-------------------------


    #[Route(path: '/animal/{id}/status/edit', name: 'animal_status_edit')]
    public function editStatus(Animals $animal, Request $request): Response
    {
        // Récupérer l'état du formulaire
        $status = $request->request->get('state');

        if ($status) {
            // Appliquer l'état à l'animal
            $animal->setState($status);

            // Sauvegarder les modifications
            $this->entityManager->flush();

            // Ajouter un message de succès
            $this->addFlash('success', 'L\'état de l\'animal a été mis à jour.');
        }

        // Rediriger vers la page de gestion des animaux
        return $this->redirectToRoute('app_vet_anim');
    }

}