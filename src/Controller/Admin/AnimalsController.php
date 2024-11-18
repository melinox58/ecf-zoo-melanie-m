<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AnimalsRepository;
use App\Repository\HabitatsRepository;
use App\Entity\Animals;
use App\Entity\Images;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Image;
use App\Service\PictureService;

class AnimalsController extends AbstractController
{
    #[Route('/admin/anim', name: 'app_admin_anim')]
    public function index(Request $request, AnimalsRepository $animalsRepository): Response
    {
        $queryBuilder = $animalsRepository->createQueryBuilder('a');
        $breedFilter = $request->query->get('breed');
        $nameFilter = $request->query->get('name');

        if ($breedFilter) {
            $queryBuilder->andWhere('a.breed = :breed')
                        ->setParameter('breed', $breedFilter);
        }

        if ($nameFilter) {
            $queryBuilder->andWhere('a.nameAnimal LIKE :name')
                        ->setParameter('name', '%' . $nameFilter . '%');
        }

        $animals = $queryBuilder->getQuery()->getResult();
        $races = $animalsRepository->createQueryBuilder('a')
            ->select('DISTINCT a.breed')
            ->getQuery()
            ->getResult();

        return $this->render('admin/animals/index.html.twig', [
            'animals' => $animals,
            'races' => $races,
        ]);
    }

    #[Route('/admin/anim/modif/{id}', name: 'admin_anim_modif')]
public function modify(
    Animals $animal,
    Request $request,
    EntityManagerInterface $entityManager,
    HabitatsRepository $habitatsRepository,
    PictureService $pictureService
): Response {
    $habitats = $habitatsRepository->findAll();

    $form = $this->createFormBuilder($animal)
    ->add('nameAnimal', TextType::class, [
        'label' => 'Nom de l\'animal',
    ])
    ->add('breed', TextType::class, [
        'label' => 'Race',
    ])
    ->add('description', TextType::class)
    ->add('idHabitats', ChoiceType::class, [
        'label' => 'Habitat',
        'choices' => array_combine(
            array_map(fn($h) => $h->getName(), $habitats),
            $habitats
        ),
        'choice_label' => fn($choice) => $choice->getName(),
        'placeholder' => 'Choisissez un habitat',
    ])
    ->add('image', FileType::class, [
        'label' => 'Image de l\'animal',
        'mapped' => false,
        'required' => false, // L'image n'est pas obligatoire
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
        'label' => 'Enregistrer'
    ])
    ->getForm();


    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form->get('image')->getData();
        
        if ($imageFile) {
            // Supprime l'ancienne image si elle existe
            foreach ($animal->getImages() as $existingImage) {
                $imagePath = $this->getParameter('uploads_directory') . '/animals/' . $existingImage->getFilePath();
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $entityManager->remove($existingImage);
            }
            
            $newFilename = $pictureService->square($imageFile, 'animals');

            $newImage = new Images();
            $newImage->setFilePath($newFilename);
            $entityManager->persist($newImage);
            $animal->addImage($newImage);
        }

        $entityManager->flush();

        $this->addFlash('success', 'Les modifications ont été enregistrées avec succès.');
        return $this->redirectToRoute('app_admin_anim');
    }

    return $this->render('admin/animals/modif.html.twig', [
        'form' => $form->createView(),
        'animal' => $animal,
    ]);
}



    #[Route('/admin/anim/delete/{id}', name: 'admin_anim_delete', methods: ['POST'])]
    public function delete(Animals $animal, EntityManagerInterface $entityManager): Response
    {
        foreach ($animal->getImages() as $image) {
            $imagePath = $this->getParameter('uploads_directory') . '/animals/' . $image->getFilePath();
            if ($imagePath && file_exists($imagePath)) {
                unlink($imagePath);
            }
            $entityManager->remove($image);
        }

        $entityManager->remove($animal);
        $entityManager->flush();

        $this->addFlash('success', 'L\'animal a été supprimé avec succès.');

        return $this->redirectToRoute('app_admin_anim');
    }





    #[Route('/admin/anim/add', name: 'admin_anim_add')]
public function add(Request $request, EntityManagerInterface $entityManager, HabitatsRepository $habitatsRepository, PictureService $pictureService): Response
{
    $animal = new Animals();
    $habitats = $habitatsRepository->findAll();

    $form = $this->createFormBuilder($animal)
        ->add('nameAnimal', TextType::class, [
            'label' => 'Nom de l\'animal',
        ])
        ->add('breed', TextType::class, [
            'label' => 'Race',
        ])
        ->add('description', TextType::class)
        ->add('idHabitats', ChoiceType::class, [
            'label' => 'Habitat',
            'choices' => array_combine(
                array_map(fn($h) => $h->getName(), $habitats),
                $habitats
            ),
            'choice_label' => fn($choice) => $choice->getName(),
            'placeholder' => 'Choisissez un habitat',
        ])
        ->add('image', FileType::class, [ 
            'label' => 'Image de l\'animal', 
            'mapped' => false, 
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
            ]
        ])
        ->add('save', SubmitType::class, ['label' => "Ajouter un animal"])
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form->get('image')->getData(); 
        
        if ($imageFile) {
            $newFilename = $pictureService->square($imageFile, 'animals');
            
            $image = new Images();
            $image->setFilePath($newFilename);
            $entityManager->persist($image);
            $animal->addImage($image);
        }

        $entityManager->persist($animal);
        $entityManager->flush();
        

        return $this->redirectToRoute('app_admin_anim');
    }

    return $this->render('admin/animals/add.html.twig', [
        'form' => $form->createView(),
    ]);
}

}
