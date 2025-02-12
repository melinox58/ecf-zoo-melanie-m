<?php
namespace App\Controller;

use App\Service\MongoDBService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OpinionController extends AbstractController
{

    #[Route('opinion/add', name: 'add_opinion', methods: ['GET', 'POST'])]
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
                    'date' => (new \DateTime())->format('d-m-Y'), // Enregistrement de la date sous forme de chaîne
                    'star' => (int)$data['etoiles'],
                    'isValidated' => false, // Avis en attente de validation
                    'created_at' => new \DateTime(), // Date de création
                ];
                
                // Insertion dans la collection MongoDB
                $collection = $mongoDBService->getCollection('opinions');
                $result = $collection->insertOne($newOpinion); // Insert the document into MongoDB

                // Vérification du succès de l'insertion
                if ($result->getInsertedCount() > 0) {
                    $this->addFlash('success', 'L\'avis a été déposé avec succès.');
                } else {
                    $this->addFlash('error', 'Une erreur s\'est produite lors de l\'ajout de l\'avis.');
                }
        
                return $this->redirectToRoute('app_home', ['_fragment' => 'avis']);
            }

            }

        return $this->render('opinion/index.html.twig'); // Ajout de cette ligne pour gérer les requêtes GET
    }
}
