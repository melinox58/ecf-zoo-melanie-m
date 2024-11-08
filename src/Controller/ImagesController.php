<?php

namespace App\Controller;

use App\Entity\Images;
use App\Form\AddImageFormType;
use App\Service\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ImagesController extends AbstractController
{
    #[Route('/add', name: 'add_image')]  // Spécifier une route claire
    public function addImage(Request $request, EntityManagerInterface $em, PictureService $pictureService): Response
    {
        $image = new Images();
        $imageForm = $this->createForm(AddImageFormType::class, $image);
        $imageForm->handleRequest($request);

        if ($imageForm->isSubmitted() && $imageForm->isValid()) {
            // Traitement de l'image téléchargée
            $picture = $imageForm->get('image')->getData();  // Assurez-vous d'utiliser le bon champ
            $pictureLoad = $pictureService->square($picture, 'images', 300);  // 'images' est le dossier cible
            $image->setName($pictureLoad);  // Enregistrement du nom de l'image

            $em->persist($image);
            $em->flush();

            $this->addFlash('success', 'Ajout effectué');
            
            // Redirection vers la page de liste des images
            return $this->redirectToRoute('image_list');  // Remplacer 'image_list' par la route correcte
        }

        return $this->render('images/index.html.twig', [
            'imageForm' => $imageForm->createView(),
        ]);
    }

    #[Route('/images', name: 'image_list')]
    public function listImages(EntityManagerInterface $em): Response
    {
        // Utilisation de la méthode du repository pour récupérer les images avec leurs relations
        $images = $em->getRepository(Images::class)->findImagesWithDetails();

        return $this->render('images/index.html.twig', [
            'images' => $images,
            'imageForm' => $this->createForm(AddImageFormType::class)->createView(), // Le formulaire d'upload d'image
        ]);
    }
}
