<?php

namespace App\Repository;

use App\Entity\Reports;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReportsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reports::class); // Utilisez Reports::class ici
    }

    // Méthode pour récupérer les rapports avec les détails associés
    public function findReportsWithDetails()
    {
        return $this->createQueryBuilder('r')
            ->join('r.idAnimals', 'a')  // Utilisation de 'idAnimals' ici
            ->addSelect('a') // Sélectionnez les détails de l'animal
            ->join('r.idFoods', 'f')
            ->addSelect('f') // Sélectionnez les détails de l'aliment
            ->join('r.idUsers', 'u')
            ->addSelect('u') // Sélectionnez les détails de l'utilisateur
            ->getQuery()
            ->getResult();
    }

    public function findReportsWithRoles(): array
    {
        return $this->createQueryBuilder('r')
            ->select('r', 'u', 'a')
            ->join('r.idUsers', 'u')
            ->join('r.idAnimals', 'a')
            ->orderBy('r.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

}
