<?php

namespace App\Repository;

use App\Entity\Reports;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

class ReportsVetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reports::class);
    }

    /**
     * Trouver les rapports vétérinaires avec les habitats et les animaux pour un utilisateur et un habitat donné.
     */
    public function findCom($user, $habitatId)
    {
        // Utilisation du QueryBuilder pour créer une requête personnalisée
        $qb = $this->createQueryBuilder('r')
            ->innerJoin('r.idUsers', 'u') // Jointure avec la table des utilisateurs
            ->innerJoin('r.idHabitats', 'h') // Jointure avec la table des habitats
            ->innerJoin('r.idAnimals', 'a') // Jointure avec la table des animaux
            ->where('u = :user') // Filtrer par utilisateur
            ->setParameter('user', $user)
            ->andWhere('h.id = :habitat') // Filtrer par habitat
            ->setParameter('habitat', $habitatId)
            ->getQuery(); // Récupérer la requête

        // Exécuter la requête et retourner les résultats
        return $qb->getResult();
    }
}
