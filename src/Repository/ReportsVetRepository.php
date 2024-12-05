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
            ->join('r.idAnimals', 'a')  // Utilisation de 'idAnimals' ici
            ->addSelect('a') // Sélectionnez les détails de l'animal
            ->join('r.idFoods', 'f')
            ->addSelect('f') // Sélectionnez les détails de l'aliment
            ->getQuery()
            ->getResult();
    }

    //Méthode qui récupère le dernier rapport vétérinaire lié à un habitat, trié par date décroissante.
    public function findLastReportByHabitat($habitatId)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.idHabitats = :habitatId')
            ->setParameter('habitatId', $habitatId)
            ->orderBy('r.date', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}


