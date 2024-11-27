<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\HabitatsRepository;
use App\Entity\Habitats;
use App\Entity\Images;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')] // Sécurise toutes les routes du contrôleur pour le rôle ADMIN
class HabController extends AbstractController
{
    #[Route('/admin/hab', name: 'app_admin_hab')]
    public function index(HabitatsRepository $habitatsRepository): Response
    {
        $habitats = $habitatsRepository->findBy([], ['name' => 'asc']);

        return $this->render('admin/habitats/index.html.twig', compact('habitats'));
    }

    #[Route('/admin/habitats/modif/{id}', name: 'admin_habitat_modif')]
    public function modify(Habitats $hab, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder($hab)
            ->add('name', TextType::class)
            ->add('description', TextType::class)
            ->add('image', FileType::class, [
                'label' => 'Image (JPG, PNG)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPG, PNG).',
                    ])
                ]
            ])
            ->add('save', SubmitType::class, ['label' => 'Enregistrer'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                // Déplacez le fichier dans le répertoire défini
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                // Créez une nouvelle entité Image
                $image = new Images();
                $image->setFilePath($newFilename);
                $image->setIdHabitats($hab);
                $entityManager->persist($image);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Les modifications ont été enregistrées avec succès.');

            return $this->redirectToRoute('app_admin_hab');
        }

        return $this->render('admin/habitats/modif.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/habitats/delete/{id}', name: 'habitat_delete', methods: ['POST'])]
    public function delete(Habitats $hab, EntityManagerInterface $entityManager): Response
    {
        // Supprime les fichiers des images associées
        foreach ($hab->getImages() as $image) {
            $imagePath = $this->getParameter('images_directory') . '/' . $image->getFilename();
            if (file_exists($imagePath)) {
                unlink($imagePath); // Supprime le fichier physique
            }
        }

        $entityManager->remove($hab);
        $entityManager->flush();

        $this->addFlash('success', 'L\'habitat a été supprimé avec succès.');
        return $this->redirectToRoute('app_admin_hab');
    }

    #[Route('/admin/habitats/add', name: 'admin_habitat_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $habitat = new Habitats();

        $form = $this->createFormBuilder($habitat)
            ->add('name', TextType::class)
            ->add('description', TextType::class)
            ->add('image', FileType::class, [
                'label' => 'Image (JPG, PNG)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPG, PNG).',
                    ])
                ]
            ])
            ->add('save', SubmitType::class, ['label' => 'Ajouter'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                // Déplacez le fichier dans le répertoire défini
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                // Créez une nouvelle entité Image
                $image = new Images();
                $image->setFilePath($newFilename);
                $image->setIdHabitats($habitat);
                $entityManager->persist($image);
            }

            $entityManager->persist($habitat);
            $entityManager->flush();

            $this->addFlash('success', 'L\'habitat a été ajouté avec succès.');

            return $this->redirectToRoute('app_admin_hab');
        }

        return $this->render('admin/habitats/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
