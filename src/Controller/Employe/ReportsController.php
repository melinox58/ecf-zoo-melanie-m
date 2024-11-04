<?php

namespace App\Controller\Employe;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AnimalsRepository;
use App\Repository\HabitatsRepository;
use App\Repository\FoodsRepository;
use App\Repository\UsersRepository;
use App\Entity\Reports;
use App\Form\ReportsType as FormReportsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class ReportsController extends AbstractController
{
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

    #[Route('/emp/report/add', name: 'emp_report_add')]
    public function add(Request $request, EntityManagerInterface $entityManager, 
        HabitatsRepository $habitatsRepository, AnimalsRepository $animalsRepository,FoodsRepository $foodsRepository, 
        UsersRepository $usersRepository, FormReportsType $report): Response
    {
        $report = new Reports();
        $animals = $animalsRepository->findAll();
        $users = $usersRepository->findAll();
        $foods = $foodsRepository->findAll();

        // Création du formulaire
        $form = $this->createForm(FormReportsType::class, $report);


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
            $report->setIdAnimals($data->getIdAnimals());
            $report->setIdUsers($data->getIdUsers());
            $report->setDate($data->getDate());
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
