<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AnimalsRepository;
use App\Repository\HabitatsRepository;
use App\Entity\Animals;
use App\Entity\Habitats;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class AnimalsController extends AbstractController
{
    #[Route('/admin/anim', name: 'app_admin_anim')]
    public function index(AnimalsRepository $animalsRepository): Response
    {
        $animals = $animalsRepository->findBy([], ['nameAnimal' =>
        'asc']);

        return $this->render('admin/animals/index.html.twig', compact
        ('animals'));
    }

    #[Route('/admin/anim/modif/{id}', name: 'admin_anim_modif')]
    public function modify(Animals $animal, Request $request, EntityManagerInterface $entityManager,HabitatsRepository $habitatsRepository): Response
    {
        // Récupération des habitats depuis la base de données
        $habitats = $habitatsRepository->findAll();

        $form = $this->createFormBuilder($animal)
        ->add('nameAnimal', TextType::class)
        ->add('breed', TextType::class)
        ->add('description', TextType::class)
        ->add('idHabitats', ChoiceType::class, [
            'choices' => array_combine(
                array_map(fn($h) => $h->getName(), $habitats),
                $habitats
            ),
            'choice_label' => function ($choice) {
                return $choice->getName(); // Afficher le nom de l'habitat
            },
            'placeholder' => 'Choisissez un habitat',
        ])
            ->add('save', SubmitType::class, ['label' => 'Enregistrer les modifications'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_anim');
        }

        return $this->render('admin/animals/modif.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/animals/delete/{id}', name: 'admin_anim_delete', methods: ['POST'])]
    public function delete(Animals $animal, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($animal);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin_anim');
    }

    #[Route('/admin/anim/add', name: 'admin_anim_add')]
    public function add(Request $request, EntityManagerInterface $entityManager, HabitatsRepository $habitatsRepository): Response
    {
        // Création d'une nouvelle instance de l'entité Animal
        $animal = new Animals();

        // Récupération des habitats depuis la base de données
        $habitats = $habitatsRepository->findAll();

        // Création du formulaire avec les sélecteurs
        $form = $this->createFormBuilder($animal)
            ->add('nameAnimal', TextType::class)
            ->add('breed', TextType::class)
            ->add('description', TextType::class)
            ->add('idHabitats', ChoiceType::class, [
                'choices' => array_combine(
                    array_map(fn($h) => $h->getName(), $habitats),
                    $habitats
                ),
                'choice_label' => function ($choice) {
                    return $choice->getName(); // Afficher le nom de l'habitat
                },
                'placeholder' => 'Choisissez un habitat',
            ])
            ->add('save', SubmitType::class, ['label' => "Ajouter un animal"])
            ->getForm();

        // Traitement de la requête
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarde du nouvel animal dans la base de données
            $entityManager->persist($animal);
            $entityManager->flush();

            // Redirection après ajout
            return $this->redirectToRoute('app_admin_anim');
        }

        // Affichage du formulaire d'ajout
        return $this->render('admin/animals/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

