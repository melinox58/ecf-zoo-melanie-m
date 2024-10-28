<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UsersRepository;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;


class EmpController extends AbstractController
{
    #[Route('/admin/emp', name: 'app_admin_emp')]
    public function index(UsersRepository $usersRepository): Response
    {
        $users = $usersRepository->findBy([], ['name' =>
        'asc']);

        return $this->render('admin/employe/index.html.twig', compact
        ('users'));
    }

    #[Route('/edition/{id}', name: 'admin_employee_edit')]
    public function edit(): Response
    {
        return $this->render('admin/employee/modif.html.twig');
    }

    #[Route('/user/delete/{id}', name: 'user_delete', methods: ['POST'])]
    public function delete(Users $user, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($user);
        $entityManager->flush(); // Met à jour la "boîte" en enlevant l'utilisateur

        return $this->redirectToRoute('app_admin_emp'); // Retour à la liste des utilisateurs
    }
}


