<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\HabitatsRepository;
use App\Entity\Habitats;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class HabController extends AbstractController
{
    #[Route('/admin/hab', name: 'app_admin_hab')]
    public function index(HabitatsRepository $habitatsRepository): Response
    {
        $habitats = $habitatsRepository->findBy([], ['name' =>
        'asc']);

        return $this->render('admin/habitats/index.html.twig', compact
        ('habitats'));
    }

    #[Route('/admin/habitats/modif/{id}', name: 'admin_habitat_modif')]
    public function modify(Habitats $hab, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder($hab)
            ->add('name', TextType::class)
            ->add('description', TextType::class)
            
            ->add('save', SubmitType::class, ['label' => 'Enregistrer les modifications'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_hab');
        }

        return $this->render('admin/habitats/modif.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/habitats/delete/{id}', name: 'habitat_delete', methods: ['POST'])]
    public function delete(Habitats $hab, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($hab);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin_hab');
    }

    #[Route('/admin/habitats/add', name: 'admin_habitat_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Création d'une nouvelle instance de l'entité Habitat
        $habitat = new Habitats();

        // Création du formulaire
        $form = $this->createFormBuilder($habitat)
            ->add('name', TextType::class)
            ->add('description', TextType::class)
            ->add('save', SubmitType::class, ['label' => "Ajouter l'habitat"])
            ->getForm();

        // Traitement de la requête
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarde du nouvel habitat dans la base de données
            $entityManager->persist($habitat);
            $entityManager->flush();

            // Redirection après ajout
            return $this->redirectToRoute('app_admin_hab');
        }

        // Affichage du formulaire d'ajout
        return $this->render('admin/habitats/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

