<?php
namespace App\Controller\Admin;

use App\Service\MongoDBService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use MongoDB\BSON\ObjectId;

class ScheduleController extends AbstractController
{
    #[Route('/admin/schedules', name: 'schedules_list', methods: ['GET'])]
    public function listSchedules(MongoDBService $mongoDBService): Response
    {
        $collection = $mongoDBService->getCollection('schedules');
        $schedules = $collection->find();

        return $this->render('admin/schedules/index.html.twig', [
            'schedules' => $schedules,
        ]);
    }

    #[Route('/admin/schedule/add', name: 'add_schedule', methods: ['GET', 'POST'])]
    public function addSchedule(Request $request, MongoDBService $mongoDBService): Response
    {
        if ($request->isMethod('POST')) {
            $data = $request->request->all();

            // Validation des données
            if (empty($data['name'])) {
                $this->addFlash('error', 'Le nom est requis.');
                return $this->redirectToRoute('add_schedule');
            }

            // Vérification des horaires
            $entries = [];
            foreach ($data['entries'] as $index => $entry) {
                if (!empty($entry['open']) && !empty($entry['close'])) {
                    $entries[] = [
                        'day' => $entry['day'],
                        'open' => $entry['open'],
                        'close' => $entry['close'],
                        'closed' => !empty($data['closed'][$index]), // Vérifie si la case de fermeture est cochée
                    ];
                }
            }

            // Préparer les données pour l'insertion dans MongoDB
            $newSchedule = [
                'name' => $data['name'],
                'entries' => $entries,
                'exceptions' => $data['exceptions'] ?? [],
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
        $schedule = $collection->findOne(['_id' => new ObjectId($id)]);

        if (!$schedule) {
            throw $this->createNotFoundException('Horaire non trouvé');
        }

        $data = []; // Initialiser $data pour éviter l'avertissement

        if ($request->isMethod('POST')) {
            $data = $request->request->all();

            // Vérification des horaires
            $entries = [];
            foreach ($data['entries'] as $index => $entry) {
                if (!empty($entry['open']) && !empty($entry['close'])) {
                    $entries[] = [
                        'day' => $entry['day'] ?? '', // Assurez-vous que "day" est défini pour éviter l'erreur
                        'open' => $entry['open'],
                        'close' => $entry['close'],
                        'closed' => !empty($data['closed'][$index]), // Vérifie si la case de fermeture est cochée
                    ];
                }
            }

            // Mise à jour du document dans la collection
            $updatedData = [
                'name' => $data['name'],
                'entries' => $entries,
                'exceptions' => $data['exceptions'] ?? [],
            ];
            $collection->updateOne(['_id' => new ObjectId($id)], ['$set' => $updatedData]);

            $this->addFlash('success', 'L\'horaire a été mis à jour avec succès.');
            return $this->redirectToRoute('schedules_list');
        }

        // Conversion du document BSON en tableau PHP et conversion de l'ID en chaîne
        $scheduleArray = json_decode(json_encode($schedule), true);
        $scheduleArray['_id'] = (string) $schedule['_id']; // Convertir ObjectId en chaîne de caractères

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
