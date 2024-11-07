<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ServicesRepository;
use App\Entity\Services;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ServController extends AbstractController
{
    #[Route('/admin/serv', name: 'app_admin_serv')]
    public function index(ServicesRepository $servicesRepository): Response
    {
        // Vérification des droits d'accès
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_EMPLOYEE')) {
            throw $this->createAccessDeniedException('Accès non autorisé.');
        }

        $services = $servicesRepository->findBy([], ['name' => 'asc']);

        return $this->render('admin/services/index.html.twig', compact('services'));
    }

    #[Route('/admin/services/modif/{id}', name: 'admin_services_modif')]
    public function modify(Services $serv, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérification des droits d'accès
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_EMPLOYEE')) {
            throw $this->createAccessDeniedException('Accès non autorisé.');
        }

        $form = $this->createFormBuilder($serv)
            ->add('name', TextType::class)
            ->add('description', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Enregistrer les modifications'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_serv');
        }

        return $this->render('admin/services/modif.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/services/delete/{id}', name: 'services_delete', methods: ['POST'])]
    public function delete(Services $serv, EntityManagerInterface $entityManager): Response
    {
        // Vérification des droits d'accès
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_EMPLOYEE')) {
            throw $this->createAccessDeniedException('Accès non autorisé.');
        }

        $entityManager->remove($serv);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin_serv');
    }

    #[Route('/admin/services/add', name: 'admin_services_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérification des droits d'accès
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_EMPLOYEE')) {
            throw $this->createAccessDeniedException('Accès non autorisé.');
        }

        // Création d'une nouvelle instance de l'entité Services
        $service = new Services();

        // Création du formulaire
        $form = $this->createFormBuilder($service)
            ->add('name', TextType::class)
            ->add('description', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Ajouter le service'])
            ->getForm();

        // Traitement de la requête
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarde du nouveau service dans la base de données
            $entityManager->persist($service);
            $entityManager->flush();

            // Redirection après ajout
            return $this->redirectToRoute('app_admin_serv');
        }

        // Affichage du formulaire d'ajout
        return $this->render('admin/services/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
