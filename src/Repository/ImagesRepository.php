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
        // Méthode pour récupérer les images avec les détails associés
      
    public function findImagesWithDetails()
    {
        return $this->createQueryBuilder('i')
            ->join('i.idServices', 's')
            ->addSelect('s')
            ->join('i.idAnimals', 'a')
            ->addSelect('a')
            ->join('i.idHabitats', 'h')
            ->addSelect('h')
            ->getQuery()
            ->getResult();
    }

}
