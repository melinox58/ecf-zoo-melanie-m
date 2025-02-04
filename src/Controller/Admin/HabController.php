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
            
            // Supprimer l'ancienne image associée à l'habitat
            if ($hab->getImages()->count() > 0) {
                // Supposons que chaque habitat n'a qu'une seule image (si vous avez plusieurs images par habitat, vous devrez adapter ceci)
                $existingImage = $hab->getImages()->first();
                $oldImagePath = $this->getParameter('images_directory_habitats') . '/' . $existingImage->getFilePath();

                // Supprimer l'ancienne image du serveur
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }

                // Supprimer l'ancienne entité Image
                $entityManager->remove($existingImage);
            }

            // Ajouter la nouvelle image, si elle a été téléchargée
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                // Déplacer l'image dans le répertoire défini
                $imageFile->move(
                    $this->getParameter('images_directory_habitats'),
                    $newFilename
                );

                // Créer une nouvelle entité Image et l'associer à l'habitat
                $image = new Images();
                $image->setFilePath($newFilename);
                $image->setIdHabitats($hab);
                $entityManager->persist($image);
            }

            // Enregistrer les modifications de l'habitat et de l'image
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
            $imagePath = $this->getParameter('images_directory_habitats') . '/' . $image->getFilename();
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
                    $this->getParameter('images_directory_habitats'),
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
