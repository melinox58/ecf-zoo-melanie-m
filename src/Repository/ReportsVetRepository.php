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
            ->leftJoin('r.idUsers', 'u') // LEFT JOIN pour inclure tous les rapports
            ->andWhere('u.id = :userId')
            ->setParameter('userId', $userId)
            ->leftJoin('r.idAnimals', 'a')
            ->addSelect('a')
            ->leftJoin('r.idFoods', 'f')
            ->addSelect('f')
            ->getQuery()
            ->getResult();
    }
    

    // Méthode qui récupère les rapports liés à un habitat, triés par date décroissante
    public function findCom($user, $habitatId)
    {
        return $this->createQueryBuilder('r')
            ->join('r.idUsers', 'u')
            ->join('r.idAnimals', 'a')
            ->join('a.idHabitats', 'h') // On joint l'habitat via l'animal
            ->where('u = :user')
            ->setParameter('user', $user)
            ->andWhere('h.id = :habitat')
            ->setParameter('habitat', $habitatId)
            ->orderBy('r.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
    

    public function findAllForUser($user)
    {
        return $this->createQueryBuilder('r')
            ->where('r.idUsers = :user')
            ->setParameter('user', $user)
            ->orderBy('r.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
    

    public function findLatestReportVetByHabitat(int $habitatId)
    {
        return $this->createQueryBuilder('r')
            ->join('r.idAnimals', 'a')
            ->join('a.idHabitats', 'h')
            ->andWhere('h.id = :habitatId')
            ->setParameter('habitatId', $habitatId)
            ->orderBy('r.date', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
    
}


