<?php

namespace App\Repository;

use App\Entity\Reports;
use App\Entity\Foods;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\FoodsRepository;

/**
 * @extends ServiceEntityRepository<Reports>
 */
class ReportsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reports::class);
    }

        
    public function findReportsWithFoods()
    {
        return $this->createQueryBuilder('r')
            ->join('r.foods', 'f') // Jointure avec l'entité Foods
            ->addSelect('f') // Sélectionner aussi les données de Foods
            ->getQuery()
            ->getResult();
    }

}
