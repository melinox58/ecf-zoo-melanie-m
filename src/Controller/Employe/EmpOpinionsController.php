<?php

namespace App\Controller\Employe;

use App\Service\MongoDBService;
use MongoDB\BSON\ObjectId; // Ajout de l'importation
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmpOpinionsController extends AbstractController
{
    #[Route('employee/opinion', name: 'emp_opinion_list', methods: ['GET'])]
    public function listOpinion(MongoDBService $mongoDBService): Response
    {
        $collection = $mongoDBService->getCollection('opinions');
        $opinions = $collection->find(); // Convertir en tableau

        return $this->render('employee/opinions/index.html.twig', [
            'opinions' => $opinions,
        ]);
    }

    #[Route('employee/opinions/edit/{id}', name: 'emp_opinion_edit', methods: ['GET', 'POST'])]
    public function editOpinion(Request $request, MongoDBService $mongoDBService, string $id): Response
    {
        $collection = $mongoDBService->getCollection('opinions');
        $opinion = $collection->findOne(['_id' => new ObjectId($id)]); // Recherche avec ObjectId

        if (!$opinion) {
            throw $this->createNotFoundException('Avis non trouvé');
        }

        if ($request->isMethod('POST')) {
            $data = $request->request->all();

            $updatedData = [
                'pseudo' => $data['pseudo'],
                'title' => $data['title'],
                'comment' => $data['comment'],
                'date' => (new \DateTime())->format('d-m-Y'),
                'isValidated' => false,
            ];
            $collection->updateOne(['_id' => new ObjectId($id)], ['$set' => $updatedData]); // Utilisation de ObjectId

            $this->addFlash('success', 'L\'avis a été modifié avec succès.');
            return $this->redirectToRoute('emp_opinion_list');
        }

        $opinionArray = json_decode(json_encode($opinion), true);

        return $this->render('employee/opinions/edit.html.twig', [
            'opinion' => $opinionArray,
        ]);
    }

    #[Route('employee/opinions/validate', name: 'emp_opinion_validate', methods: ['GET'])]
    public function validateOpinions(MongoDBService $mongoDBService): Response
    {
        $collection = $mongoDBService->getCollection('opinions');
        $opinions = $collection->find(['isValidated' => false]);

        return $this->render('employee/opinions/index.html.twig', [
            'opinions' => $opinions,
        ]);
    }

    #[Route('employee/opinion/approve/{id}', name: 'emp_opinion_approve', methods: ['POST'])]
    public function approveOpinion(string $id, MongoDBService $mongoDBService): Response
    {
        $collection = $mongoDBService->getCollection('opinions');
        $result = $collection->updateOne(['_id' => new ObjectId($id)], ['$set' => ['isValidated' => true]]); // Marquer l'avis comme validé

        if ($result->getModifiedCount() > 0) {
            $this->addFlash('success', 'L\'avis a été approuvé avec succès.');
        } else {
            $this->addFlash('error', 'L\'avis n\'a pas pu être approuvé.');
        }

        return $this->redirectToRoute('emp_opinion_validate');
    }

    #[Route('/opinion/delete/{id}', name: 'emp_opinion_delete', methods: ['POST'])]
    public function deleteOpinion(MongoDBService $mongoDBService, string $id): Response
    {
        $collection = $mongoDBService->getCollection('opinions');
        $collection->deleteOne(['_id' => new ObjectId($id)]); // Utilisation de ObjectId

        $this->addFlash('success', 'L\'avis a été supprimé avec succès.');
        return $this->redirectToRoute('emp_opinion_list');
    }
}
