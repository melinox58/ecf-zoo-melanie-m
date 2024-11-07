<?php

namespace App\Controller\Employe;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AnimalsRepository;
use App\Repository\UsersRepository;
use App\Entity\Users;
use App\Entity\Reports;
use App\Form\ReportsType as FormReportsType;
use App\Repository\ReportsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class ReportsController extends AbstractController
{
    private $animalsRepository;

    public function __construct(AnimalsRepository $animalsRepository)
    {
        $this->animalsRepository = $animalsRepository; // Injecter le AnimalsRepository
    }
    
    #[Route('/emp/report', name: 'app_emp_anim')]
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

        return $this->render('employee/reports/index.html.twig', [
            'animals' => $animals,
            'races' => $races,
        ]);
    }

    #[Route('/emp/report/add/{id}', name: 'emp_report_add')]
    public function add(Request $request, EntityManagerInterface $entityManager, UsersRepository $usersRepository, $id): Response
    {
        $report = new Reports();

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
        $form = $this->createForm(FormReportsType::class, $report, [
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


            return $this->render('employee/reports/add.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        #[Route('/employee/reports/list', name: 'app_employee_reports_list')]
        public function reports(ReportsRepository $reportsRepository): Response
        {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Vérifier si l'utilisateur est connecté
        if (!$user) {
            return $this->redirectToRoute('app_login'); // Rediriger vers la page de connexion
        }

            // Récupérer les rapports avec les détails associés
            $reports = $reportsRepository->findReportsWithDetails();

            return $this->render('employee/reports/list.html.twig', [
                'reports' => $reports,
            ]);
        }
}

