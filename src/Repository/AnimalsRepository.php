<?php

namespace App\Repository;

use App\Entity\Animals;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

/**
 * @extends ServiceEntityRepository<Animals>
 */
class AnimalsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Animals::class);
    }

   /**
    * @return Animals[] Returns an array of Animals objects
    */
   public function findid($id): array
   {
       return $this->createQueryBuilder('a')
           ->andWhere('a.id = :val')
           ->setParameter('val', $id)
           ->orderBy('a.id', 'ASC')
           ->setMaxResults(10)
           ->getQuery()
           ->getResult()
       ;
   }

   public function showReportsForAnimal(Animals $animal): Response
    {
        $report = $animal->getReports();

        return $this->render('animal/reports.html.twig', [
            'animal' => $animal,
            'reports' => $report,
        ]);
    }

    public function updateAnimalStateFromReportsVet(): static
    {
        if ($this->idReportsVet->isEmpty()) {
            return $this;
        }
    
        // Prendre en compte l'état du dernier rapport vétérinaire, par exemple
        $latestReportVet = $this->idReportsVet->last();
        if ($latestReportVet && $latestReportVet->getState()) {
            $this->state = $latestReportVet->getState();
        }
    
        return $this;
    }
    
}
