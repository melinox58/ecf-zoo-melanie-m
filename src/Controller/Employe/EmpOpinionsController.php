<?php

namespace App\Controller\Employe;

use App\Service\MongoDBService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use MongoDB\BSON\ObjectId;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmpOpinionsController extends AbstractController
{
    #[Route('employee/opinions', name: 'emp_opinion_list', methods: ['GET'])]
    public function listOpinion(MongoDBService $mongoDBService): Response
    {
        // Récupérer tous les avis (validés et non validés)
        $collection = $mongoDBService->getCollection('opinions');
        $opinions = $collection->find([], ['sort' => ['created_at' => -1]]); // Trie par date décroissante
    
        return $this->render('employee/opinions/index.html.twig', [
            'opinions' => $opinions,
        ]);
    }
    

    #[Route('employee/opinions/validate', name: 'emp_opinion_validate', methods: ['GET'])]
    public function validateOpinions(MongoDBService $mongoDBService): Response
{
    $collection = $mongoDBService->getCollection('opinions');
    $opinions = $collection->find(['isValidated' => false], ['sort' => ['created_at' => -1]]);

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
