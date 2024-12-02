<?php

namespace App\Repository;

use App\Entity\ReportsVet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class ReportsVetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReportsVet::class);
    }

    // Méthode pour récupérer les rapports vétérinaires par utilisateur
    public function findReportsVetByUserId(int $userId): array
    {
        return $this->createQueryBuilder('r')
            ->join('r.idUsers', 'u') // Jointure avec l'entité Users
            ->andWhere('u.id = :userId') // Filtrer par l'ID de l'utilisateur
            ->setParameter('userId', $userId) // Utilisez 'userId' pour définir la valeur
            ->getQuery()
            ->getResult();
    }
}


