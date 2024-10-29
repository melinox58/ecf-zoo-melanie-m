<?php
// src/Controller/Admin/ScheduleController.php
namespace App\Controller\Admin;

use App\Service\MongoDBService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ScheduleController extends AbstractController
{
    #[Route('/admin/schedules', name: 'schedules_list', methods: ['GET'])]
    public function listSchedules(MongoDBService $mongoDBService): Response
    {
        $collection = $mongoDBService->getCollection('schedules');
        $schedules = $collection->find()->toArray();

        return $this->render('admin/schedules/index.html.twig', [
            'schedules' => $schedules,
        ]);
    }

    #[Route('/admin/schedule/add', name: 'add_schedule', methods: ['GET', 'POST'])]
    public function addSchedule(Request $request, MongoDBService $mongoDBService): Response
    {
        if ($request->isMethod('POST')) {
            // Récupérer les données du formulaire
            $data = $request->request->all();
    
            // Préparer les données pour l'insertion dans MongoDB
            $newSchedule = [
                'name' => $data['name'],
                'entries' => $data['entries'], // Les horaires par jour
                'exceptions' => $data['exceptions'] ?? [], // Les exceptions
            ];
    
            // Insertion dans la collection MongoDB
            $collection = $mongoDBService->getCollection('schedules');
            $collection->insertOne($newSchedule);
    
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

            // Préparer les données pour l'insertion dans MongoDB
            $updatedData = [
                'name' => $data['name'],
                'entries' => $data['entries'],
                'exceptions' => $data['exceptions'] ?? [],
            ];

            // Mettre à jour le document dans la collection
            $collection->updateOne(['_id' => new \MongoDB\BSON\ObjectId($id)], ['$set' => $updatedData]);

            return $this->redirectToRoute('schedules_list');
        }

        // Convertir les BSONArray en tableaux PHP normaux
        $schedule['entries'] = iterator_to_array($schedule['entries']);
        $schedule['exceptions'] = iterator_to_array($schedule['exceptions']);

        return $this->render('admin/schedules/modif.html.twig', [
            'schedule' => $schedule,
        ]);
    }



    #[Route('/admin/schedule/delete/{id}', name: 'schedule_delete', methods: ['POST'])]
    public function deleteSchedule(MongoDBService $mongoDBService, string $id): Response
    {
        $collection = $mongoDBService->getCollection('schedules');
        $collection->deleteOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);

        return $this->redirectToRoute('schedules_list');
    }
}
