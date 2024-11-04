<?php

namespace App\Repository;

use App\Entity\Report;
use App\Form\ReportsType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReportsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReportsType::class);
    }

    // Méthode pour récupérer les rapports avec les détails associés
    public function findReportsWithDetails()
    {
        return $this->createQueryBuilder('r')
            ->join('r.animal', 'a')
            ->addSelect('a') // Sélectionnez les détails de l'animal
            ->join('r.food', 'f')
            ->addSelect('f') // Sélectionnez les détails de l'aliment
            ->join('r.user', 'u')
            ->addSelect('u') // Sélectionnez les détails de l'utilisateur
            ->getQuery()
            ->getResult();
    }
}
