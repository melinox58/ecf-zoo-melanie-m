<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UsersRepository;

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
}

