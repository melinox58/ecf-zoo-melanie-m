<?php

namespace App\Controller\Employe;

use App\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AnimalsRepository;
use App\Repository\UsersRepository;
use App\Entity\Reports;
use App\Form\ReportsType as FormReportsType;
use App\Repository\ReportsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class ReportsController extends AbstractController
{
    private $animalsRepository;
    private $reportsRepository;
    private $entityManager;

    public function __construct(AnimalsRepository $animalsRepository, ReportsRepository $reportsRepository, EntityManagerInterface $entityManager)
    {
        $this->animalsRepository = $animalsRepository;
        $this->reportsRepository = $reportsRepository;
        $this->entityManager = $entityManager;
    }

    
    #[Route('/emp/report', name: 'app_emp_anim')]
    public function report(Request $request, AnimalsRepository $animalsRepository): Response
    {
        $queryBuilder = $animalsRepository->createQueryBuilder('a');

        // Récupérer les filtres depuis la requête
        $breedFilter = $request->query->get('breed');
        $nameFilter = $request->query->get('name');

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
    public function add(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $report = new Reports();

        // Récupérer l'animal par ID
        $animal = $this->animalsRepository->find($id);
        
        if (!$animal) {
            throw $this->createNotFoundException('Animal non trouvé');
        }

        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Création du formulaire
        $form = $this->createForm(FormReportsType::class, $report, [
            'user' => $user,
            'animal' => $animal,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Définir les propriétés du rapport
            $report->setWeight($data->getWeight());
            $report->setUnit($data->getUnit());
            $report->setIdAnimals($animal);
            $report->setIdUsers($user);
            $report->setDate(new \DateTime());
            $report->setComment($data->getComment());

            // Sauvegarder le rapport dans la base de données
            $entityManager->persist($report);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_emp_anim');
        }

        return $this->render('employee/reports/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Méthode pour afficher les rapports d'un utilisateur spécifique
    #[Route('/employee/reports/list/{userId}', name: 'app_employee_reports_list')]
    public function reports($userId, ReportsRepository $reportsRepository): Response
    {
        // Récupérer l'utilisateur par son ID
        $user = $this->entityManager->getRepository(Users::class)->find($userId);
            $user = $this->getUser();

            if (!$user) {
                return $this->redirectToRoute('app_login');
            }

            $reports = [];
            
            if ($this->isGranted('ROLE_ADMIN')) {
                // Administrateur : Tous les rapports avec les rôles
                $reports = $reportsRepository->findReportsWithRoles();
            } elseif ($this->isGranted('ROLE_VETERINARY')) {
                // Vétérinaire : Ses propres rapports
                $reports = $reportsRepository->findBy(['idUsers' => $user], ['date' => 'DESC']);
            } else {
                // Employé : Rapports liés à ses animaux
                $reports = $reportsRepository->findReportsForEmployee($user->getId());
            }
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        // Récupérer les rapports de cet utilisateur spécifique
        $reports = $this->reportsRepository->findReportsByCreator($user);

        return $this->render('employee/reports/list.html.twig', [
            'reports' => $reports,
            'user' => $user,
        ]);
    }
}
