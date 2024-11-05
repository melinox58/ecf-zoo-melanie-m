<?php

namespace App\Controller;

use App\Service\MongoDBService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use MongoDB\BSON\DateTime;


class OpinionController extends AbstractController
{
    #[Route('/opinion', name: 'opinion_list', methods: ['GET'])]
    public function listOpinion(MongoDBService $mongoDBService): Response
    {
        $collection = $mongoDBService->getCollection('opinions');
        $opinions = $collection->find();

        return $this->render('opinion/list.html.twig', [
            'opinions' => $opinions,
        ]);
    }

    #[Route('/opinion/add', name: 'add_opinion', methods: ['GET', 'POST'])]
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
                $dateString = (new \DateTime())->format('d-m-Y H:i:s'); // Format de la date souhaité
                $newOpinion = [
                    'pseudo' => $data['pseudo'],
                    'title' => $data['title'],
                    'comment' => $data['comment'],
                    'date' => (new \DateTime())->format('d-m-Y'), // Enregistrement de la date sous forme de chaîne
                ];
        
                // Insertion dans la collection MongoDB
                $collection = $mongoDBService->getCollection('opinions');
                $collection->insertOne($newOpinion);
        
                $this->addFlash('success', 'L\'avis a été déposé avec succès.');
                return $this->redirectToRoute('app_home', ['_fragment' => 'avis']);
            }
        }

        return $this->render('opinion/add.html.twig'); // Ajout de cette ligne pour gérer les requêtes GET
    }

}
