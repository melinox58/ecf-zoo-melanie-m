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
            // Rediriger vers une page d'erreur ou de connexion
            return $this->redirectToRoute('app_login'); // Remplacez 'app_login' par votre route de connexion
        }

        // Création du formulaire et passage de l'utilisateur
        $form = $this->createForm(FormReportsType::class, $report, [
            'user' => $user, // Passer l'utilisateur connecté ici
            'animal' => $animal, // Passer l'animal récupéré ici
        ]);

        // Traitement de la requête
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Récupérer l'aliment sélectionné
            $selectedFood = $data->getIdFoods();
            if ($selectedFood) {
                // Définir le poids et l'unité à partir de l'aliment sélectionné
                $report->setWeight($selectedFood->getWeight());
                $report->setUnit($selectedFood->getUnit());
            }

            // Assurez-vous que vous récupérez correctement les autres valeurs
            $report->setIdAnimals($animal);
            $report->getIdUsers(); // Utiliser l'utilisateur connecté
            $report->setDate(new \DateTime()); // Vous pouvez définir la date ici ou dans le formulaire
            $report->setComment($data->getComment());

            // Persister le rapport
            $entityManager->persist($report);
            $entityManager->flush();

            return $this->redirectToRoute('app_emp_anim');
        }

        return $this->render('employee/reports/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
