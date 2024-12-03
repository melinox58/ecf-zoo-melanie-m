<?php

namespace App\Controller\Veterinary;

use App\Entity\ReportsVet;
use App\Repository\HabitatsRepository;
use App\Repository\ReportsVetRepository;
use App\Repository\UsersRepository;
use App\Form\ReportsVetType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VeterinaryController extends AbstractController
{
    private $habitatsRepository;
    private $reportsVetRepository;
    private $usersRepository;

    public function __construct(
        HabitatsRepository $habitatsRepository,
        ReportsVetRepository $reportsVetRepository,
        UsersRepository $usersRepository
    ) {
        $this->habitatsRepository = $habitatsRepository;
        $this->reportsVetRepository = $reportsVetRepository;
        $this->usersRepository = $usersRepository;
    }

    #[Route('/veterinary', name: 'app_veterinary')]
    public function index(Request $request): Response
    {
        if (!$this->isGranted('ROLE_VETERINARY')) {
            return $this->redirectToRoute('app_home');
        }

        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Accès aux données de l'utilisateur
        $username = $user->getFirstName() . ' ' . $user->getName();  // Utilisation des getters ici

        $habitats = $this->habitatsRepository->findAll();

        $selectedHabitatId = $request->query->get('habitat');
        $selectedHabitat = $selectedHabitatId ? $this->habitatsRepository->find($selectedHabitatId) : null;

        if ($selectedHabitat) {
            $reports = $this->reportsVetRepository->findReportsVetByHabitat($selectedHabitat);
        } else {
            $reports = $this->reportsVetRepository->findAll();
        }

        return $this->render('veterinary/index.html.twig', [
            'user' => $user,
            'username' => $username,
            'habitats' => $habitats,
            'selectedHabitat' => $selectedHabitat,
            'reports' => $reports,

        
        ]);
    }

    #[Route('/report/vet/new', name: 'app_report_vet_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reportVet = new ReportsVet();

        $user = $this->getUser(); // Assurez-vous d'obtenir l'utilisateur connecté

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Associer l'utilisateur au rapport
        $reportVet->setIdUsers($user);

        // Créer et traiter le formulaire
        $form = $this->createForm(ReportsVetType::class, $reportVet, [
            'user' => $user,
            'habitats' => $this->habitatsRepository->findAll(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reportVet);
            $entityManager->flush();

            // Ajouter un message flash de succès
            $this->addFlash('success', 'Le rapport a été créé avec succès.');

            return $this->redirectToRoute('app_vet_reports_list');
        }

        return $this->render('veterinary/comHab/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/veterinary/comHab', name: 'app_vet_reports_list')]
    public function listReportsByUser(Request $request): Response
    {
        $user = $this->getUser();

        if (!$user || !$this->isGranted('ROLE_VETERINARY')) {
            return $this->redirectToRoute('app_home');
        }

        $reports = $this->reportsVetRepository->findReportsVetByUserId($user->getId());

        return $this->render('veterinary/comHab/list.html.twig', [
            'reports' => $reports,
            'user' => $user
        ]);
    }

    #[Route('/veterinary/comHab/delete/{id}', name: 'vet_comHab_delete', methods: ['POST'])]
    public function deleteOpinion(EntityManagerInterface $entityManager, int $id): Response
    {
        // Récupérer le rapport par ID
        $report = $this->reportsVetRepository->find($id);

        if (!$report) {
            // Si le rapport n'existe pas, rediriger avec un message d'erreur
            $this->addFlash('error', 'Le rapport n\'existe pas.');
            return $this->redirectToRoute('app_vet_reports_list');
        }

        // Supprimer le rapport
        $entityManager->remove($report);
        $entityManager->flush();

        // Ajouter un message flash de succès
        $this->addFlash('success', 'Le rapport a été supprimé avec succès.');

        return $this->redirectToRoute('app_vet_reports_list');
    }


}
