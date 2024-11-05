<?php

namespace App\Controller\Employe;

use App\Service\MongoDBService;
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
        $opinions = $collection->find()->toArray(); // Convertir en tableau

        return $this->render('employee/opinions/index.html.twig', [
            'opinions' => $opinions,
        ]);
    }

    #[Route('employee/opinions/edit/{id}', name: 'emp_opinion_edit', methods: ['GET', 'POST'])]
    public function editOpinion(Request $request, MongoDBService $mongoDBService, string $id): Response
    {
        $collection = $mongoDBService->getCollection('opinions');

        // Récupérer l'avis spécifique par son identifiant
        $opinion = $collection->findOne(['id' => $id]); // Utilisez la chaîne pour chercher

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
                'date' => (new \DateTime())->format('d-m-Y'), // Format de date
            ];
            $collection->updateOne(['id' => $id], ['$set' => $updatedData]); // Utilisez la chaîne pour mettre à jour

            $this->addFlash('success', 'L\'avis a été modifié avec succès.');
            return $this->redirectToRoute('emp_opinion_list'); // Corriger la route de redirection
        }

        // Conversion du document BSON en tableau PHP pour Twig
        $opinionArray = json_decode(json_encode($opinion), true);
        
        return $this->render('employee/opinions/edit.html.twig', [
            'opinion' => $opinionArray,
        ]);
    }

    #[Route('/opinion/delete/{id}', name: 'emp_opinion_delete', methods: ['POST'])]
    public function deleteOpinion(MongoDBService $mongoDBService, string $id): Response
    {
        $collection = $mongoDBService->getCollection('opinions');
        $collection->deleteOne(['id' => $id]); // Utilisez la chaîne pour supprimer

        $this->addFlash('success', 'L\'avis a été supprimé avec succès.');
        return $this->redirectToRoute('emp_opinion_list'); // Corriger la route de redirection
    }
}
