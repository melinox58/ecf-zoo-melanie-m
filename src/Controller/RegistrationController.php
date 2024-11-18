<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si des rôles ont été sélectionnés
            $roles = $form->get('roles')->getData();
            if (empty($roles)) {
                $this->addFlash('error', 'Veuillez sélectionner au moins un rôle (par exemple : Employee ou Veterinary).');
                return $this->redirectToRoute('app_register'); // Redirige l'utilisateur vers le formulaire si aucun rôle n'est sélectionné
            }            

            // Encoder le mot de passe
            $plaintextPassword = $form->get('plainPassword')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $plaintextPassword);
            $user->setPassword($hashedPassword);

            // Récupérer les rôles du formulaire
            $user->setRoles($roles);

            // Sauvegarder l'utilisateur
            $entityManager->persist($user);
            $entityManager->flush();

            // Ajouter un message flash de succès
            $this->addFlash('success', 'L\'utilisateur a été enregistré avec succès.');

            // Rediriger vers la page admin des employés ou une autre page
            return $this->redirectToRoute('app_admin_emp');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
