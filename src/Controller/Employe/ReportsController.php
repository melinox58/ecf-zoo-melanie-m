<?php

namespace App\Controller\Employe;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AnimalsRepository;
use App\Repository\HabitatsRepository;
use App\Repository\ReportsRepository;
use App\Repository\FoodsRepository;
use App\Repository\UsersRepository;
use App\Entity\Animals;
use App\Entity\Habitats;
use App\Entity\Reports;
use App\Entity\Foods;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

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

    #[Route('/admin/anim/add', name: 'admin_anim_add')]
    public function add(Request $request, EntityManagerInterface $entityManager, 
        HabitatsRepository $habitatsRepository, AnimalsRepository $animalsRepository, 
        ReportsRepository $reportsRepository, FoodsRepository $foodsRepository, 
        UsersRepository $usersRepository): Response
    {
        $report = new Reports();
        $animals = $animalsRepository->findAll();
        $users = $usersRepository->findAll();

        // Création du formulaire
        $form = $this->createFormBuilder($report)
            ->add('idAnimals', ChoiceType::class, [
                'choices' => array_combine(
                    array_map(fn($animal) => $animal->getNameAnimal(), $animals),
                    $animals
                ),
                'choice_label' => function ($choice) {
                    return $choice->getNameAnimal();
                },
                'placeholder' => 'Choisissez un animal',
            ])
            ->add('idUsers', ChoiceType::class, [
                'choices' => array_combine(
                    array_map(fn($user) => $user->getName(), $users),
                    $users
                ),
                'choice_label' => function ($choice) {
                    return $choice->getName();
                },
                'placeholder' => 'Choisissez un employé',
            ])
            ->add('idFoods', EntityType::class, [
                'class' => Foods::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisissez un aliment',
                'mapped' => true,
            ])
            ->add('date', DateTimeType::class, [
                'data' => new \DateTime(),
                'widget' => 'single_text',
                'html5' => true,
                'attr' => ['readonly' => true],
            ])
            ->add('comment', TextType::class, [
                'mapped' => true,
            ])
            ->add('save', SubmitType::class, ['label' => "Ajouter un rapport"])
            ->getForm();

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
