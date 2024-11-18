<?php

namespace App\Repository;

use App\Entity\Images;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Images>
 */
class ImagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Images::class);
    }
    
    public function findImagesWithDetails()
    {
        return $this->createQueryBuilder('i')
            // Utilisation de leftJoin pour éviter les erreurs si des relations manquent
            ->leftJoin('i.idServices', 's')
            ->addSelect('s')
            ->leftJoin('i.idAnimals', 'a')
            ->addSelect('a')
            ->leftJoin('i.idHabitats', 'h')
            ->addSelect('h')
            ->getQuery()
            ->getResult();
    }
}
