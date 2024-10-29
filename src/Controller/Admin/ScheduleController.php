<?php
// src/Controller/Admin/ScheduleController.php
namespace App\Controller\Admin;

use App\Service\MongoDBService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use MongoDB\BSON\ObjectId;
use MongoDB\Driver\Exception\Exception;

class ScheduleController extends AbstractController
{
    #[Route('/admin/schedules', name: 'schedules_list', methods: ['GET'])]
    public function listSchedules(MongoDBService $mongoDBService): Response
    {
        $collection = $mongoDBService->getCollection('schedules');
        $schedules = $collection->find()->toArray();

        // Conversion des documents BSON en tableaux PHP
        $schedulesArray = array_map(fn($schedule) => json_decode(json_encode($schedule), true), $schedules);

        return $this->render('admin/schedules/index.html.twig', [
            'schedules' => $schedulesArray,
        ]);
    }



    #[Route('/admin/schedule/add', name: 'add_schedule', methods: ['GET', 'POST'])]
    public function addSchedule(Request $request, MongoDBService $mongoDBService): Response
    {
        if ($request->isMethod('POST')) {
            // Récupérer les données du formulaire
            $data = $request->request->all();

            // Validation des données
            if (empty($data['name'])) {
                $this->addFlash('error', 'Le nom est requis.');
                return $this->redirectToRoute('add_schedule');
            }

            // Préparer les données pour l'insertion dans MongoDB
            $newSchedule = [
                'name' => $data['name'],
                'entries' => $data['entries'], // Les horaires par jour
                'exceptions' => $data['exceptions'] ?? [], // Les exceptions
            ];

            // Insertion dans la collection MongoDB
            $collection = $mongoDBService->getCollection('schedules');
            $collection->insertOne($newSchedule);

            $this->addFlash('success', 'L\'horaire a été ajouté avec succès.');
            return $this->redirectToRoute('schedules_list');
        }

        return $this->render('admin/schedules/add.html.twig');
    }

    #[Route('/admin/schedule/edit/{id}', name: 'admin_schedule_modify', methods: ['GET', 'POST'])]
    public function editSchedule(Request $request, MongoDBService $mongoDBService, string $id): Response
    {
        $collection = $mongoDBService->getCollection('schedules');
        $schedule = $collection->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);

        if ($request->isMethod('POST')) {
            // Récupérer les données du formulaire
            $data = $request->request->all();

            // Préparer les données pour la mise à jour dans MongoDB
            $updatedData = [
                'name' => $data['name'],
                'entries' => $data['entries'],
                'exceptions' => $data['exceptions'] ?? [],
            ];

            // Mise à jour du document dans la collection
            $collection->updateOne(['_id' => new \MongoDB\BSON\ObjectId($id)], ['$set' => $updatedData]);

            return $this->redirectToRoute('schedules_list');
        }

        // Conversion du document BSON en tableau PHP
        $scheduleArray = json_decode(json_encode($schedule), true);

        return $this->render('admin/schedules/modif.html.twig', [
            'schedule' => $scheduleArray,
        ]);
    }



    
    #[Route('/admin/schedule/delete/{id}', name: 'schedule_delete', methods: ['POST'])]
    public function deleteSchedule(MongoDBService $mongoDBService, string $id): Response
    {
        $collection = $mongoDBService->getCollection('schedules');
        $collection->deleteOne(['_id' => new ObjectId($id)]);

        $this->addFlash('success', 'L\'horaire a été supprimé avec succès.');
        return $this->redirectToRoute('schedules_list');
    }
}
