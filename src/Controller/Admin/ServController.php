<?php

namespace App\Controller\Admin;

use App\Entity\Images;
use App\Service\PictureService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ServicesRepository;
use App\Entity\Services;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Image;

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
    public function modify(Services $serv, Request $request, EntityManagerInterface $entityManager, PictureService $pictureService, ServicesRepository $servicesRepository): Response
    {
        // Vérification des droits d'accès
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_EMPLOYEE')) {
            throw $this->createAccessDeniedException('Accès non autorisé.');
        }
    
        $form = $this->createFormBuilder($serv)
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('image', FileType::class, [
                'label' => 'Image de l\'animal',
                'mapped' => false,
                'required' => false,
                'attr' => ['accept' => 'image/png, image/jpeg, image/webp'],
                'constraints' => [
                    new Image(
                        minWidth: 100,
                        maxWidth: 7000,
                        minHeight: 100,
                        maxHeight: 7000,
                        allowPortrait: false,
                        mimeTypes: ['image/jpeg', 'image/png', 'image/webp']
                    )
                ],
            ])
            ->add('save', SubmitType::class, ['label' => 'Enregistrer'])
            ->getForm();
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
    
            if ($imageFile) {
                // Supprimer les anciennes images si le service en a
                foreach ($serv->getImages() as $existingImage) {
                    $imagePath = $this->getParameter('uploads_directory') . '/services/' . $existingImage->getFilePath();
                    if (file_exists($imagePath)) {
                        unlink($imagePath);  // Supprime l'ancienne image
                    }
                    $entityManager->remove($existingImage);  // Retire l'ancienne image du service
                }
    
                // Traiter la nouvelle image
                $newFilename = $pictureService->square($imageFile, 'services');
    
                $newImage = new Images();
                $newImage->setFilePath($newFilename);
                $entityManager->persist($newImage);
                $serv->addImage($newImage);  // Ajoute la nouvelle image au service
            }
    
            $entityManager->flush();
            
            // Ajouter un message flash de succès
            $this->addFlash('success', 'Le service a bien été modifié.');
    
            return $this->redirectToRoute('app_admin_serv');
        }
    
        return $this->render('admin/services/modif.html.twig', [
            'form' => $form->createView(),
            'service' => $serv,
        ]);
    }
    

    #[Route('/services/delete/{id}', name: 'services_delete', methods: ['POST'])]
    public function delete(Services $serv, EntityManagerInterface $entityManager): Response
    {
        // Vérification des droits d'accès
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_EMPLOYEE')) {
            throw $this->createAccessDeniedException('Accès non autorisé.');
        }
    
        // Récupérer les images associées à ce service
        $images = $serv->getImages();
    
        // Supprimer chaque image
        foreach ($images as $image) {
            $entityManager->remove($image);
        }
    
        // Ensuite, supprimer le service
        $entityManager->remove($serv);
        $entityManager->flush();
    
        // Ajouter un message flash de succès
        $this->addFlash('success', 'Le service a bien été supprimé.');
    
        return $this->redirectToRoute('app_admin_serv');
    }
    

    #[Route('/admin/services/add', name: 'admin_services_add')]
    public function add(Request $request, EntityManagerInterface $entityManager, PictureService $pictureService): Response
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
            ->add('description', TextareaType::class)
            ->add('image', FileType::class, [
                'label' => 'Image du service',
                'mapped' => false,
                'required' => false,
                'attr' => ['accept' => 'image/png, image/jpeg, image/webp'],
                'constraints' => [
                    new Image(
                        minWidth: 100,
                        maxWidth: 7000,
                        minHeight: 100,
                        maxHeight: 7000,
                        allowPortrait: false,
                        mimeTypes: ['image/jpeg', 'image/png', 'image/webp']
                    )
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Ajouter',
                'attr' => ['class' => 'btn btn-success']])
            ->getForm();
    
        // Traitement de la requête
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Si une image est téléchargée
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                // Traiter la nouvelle image (par exemple, redimensionner, renommer)
                $newFilename = $pictureService->square($imageFile, 'services'); // Assurez-vous que cette méthode fonctionne
    
                // Créer une nouvelle image
                $newImage = new Images();
                $newImage->setFilePath($newFilename);
    
                // Persister l'image
                $entityManager->persist($newImage);
                $service->addImage($newImage);  // Associe l'image au service
            }
    
            // Sauvegarde du service dans la base de données
            $entityManager->persist($service);
            $entityManager->flush();
    
            // Ajouter un message flash de succès
            $this->addFlash('success', 'Le service a bien été ajouté.');
    
            // Redirection après ajout
            return $this->redirectToRoute('app_admin_serv');
        }
    
        // Affichage du formulaire d'ajout
        return $this->render('admin/services/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
}
