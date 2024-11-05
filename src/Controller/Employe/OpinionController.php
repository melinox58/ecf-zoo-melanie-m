<?php

namespace App\Controller\Employe;

use App\Service\MongoDBService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use MongoDB\BSON\ObjectId;

class OpinionController extends AbstractController
{
    #[Route('employee/opinion', name: 'opinion_list', methods: ['GET'])]
    public function listOpinion(MongoDBService $mongoDBService): Response
    {
        $collection = $mongoDBService->getCollection('opinions');
        $opinions = $collection->find();

        return $this->render('employee/opinion/list.html.twig', [
            'opinions' => $opinions,
        ]);
    }






    #[Route('employee/opinion/add', name: 'add_opinion', methods: ['GET', 'POST'])]
    public function addOpinion(Request $request, MongoDBService $mongoDBService): Response
    {
        if ($request->isMethod('POST')) {
            $data = $request->request->all();
            
            // Validation des données
            if (empty($data['pseudo'])) {
                $this->addFlash('error', 'Le pseudo est requis.');
            } elseif (empty($data['title'])) {
                $this->addFlash('error', 'Un titre est requis.');
            } elseif (empty($data['comment'])) {
                $this->addFlash('error', 'Un commentaire est requis.');
            } else {
                // Préparer les données pour l'insertion dans MongoDB
                $newOpinion = [
                    'pseudo' => $data['pseudo'],
                    'title' => $data['title'],
                    'comment' => $data['comment'],
                    'date' => $data[new \DateTime()],
                ];
        
                // Insertion dans la collection MongoDB
                $collection = $mongoDBService->getCollection('opinions');
                $collection->insertOne($newOpinion);
        
                $this->addFlash('success', 'L\'avis a été déposé avec succès.');
                return $this->redirectToRoute('app_home', [], 302, '#avis'); // Redirection avec fragment
            }
        }

        return $this->render('employee/opinion/add.html.twig'); // Ajout de cette ligne pour gérer les requêtes GET
    }





    #[Route('employee/opinion/edit/{id}', name: 'opinion_edit', methods: ['GET', 'POST'])]
    public function editOpinion(Request $request, MongoDBService $mongoDBService, string $id): Response
    {
        $collection = $mongoDBService->getCollection('opinions');
        $opinion = $collection->findOne(['_id' => new ObjectId($id)]);

        if (!$opinion) {
            throw $this->createNotFoundException('Avis non trouvé');
        }

        if ($request->isMethod('POST')) {
            $data = $request->request->all();

            // Mise à jour du document dans la collection
            $updatedData = [
                'pseudo' => $data['pseudo'],
                'title' => $data['title'],
                'comment' => $data['comment'],
            ];
            $collection->updateOne(['_id' => new ObjectId($id)], ['$set' => $updatedData]);

            $this->addFlash('success', 'L\'avis a été modifié avec succès.');
            return $this->redirectToRoute('opinion_edit');
        }

        // Conversion du document BSON en tableau PHP pour Twig
        $opinionArray = json_decode(json_encode($opinion), true);
        $opinionArray['_id'] = (string) $opinion['_id']; // Convertir ObjectId en chaîne de caractères

        return $this->render('employee/opinion/list.html.twig', [
            'opinion' => $opinionArray,
        ]);
    }






    #[Route('employee/opinion/delete/{id}', name: 'opinion_delete', methods: ['POST'])]
    public function deleteOpinion(MongoDBService $mongoDBService, string $id): Response
    {
        $collection = $mongoDBService->getCollection('opinions');
        $collection->deleteOne(['_id' => new ObjectId($id)]);

        $this->addFlash('success', 'L\'avis a été supprimé avec succès.');
        return $this->redirectToRoute('opinion_list');
    }
}
