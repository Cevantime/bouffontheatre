<?php

namespace App\Repository;

use App\Entity\Performance;
use App\Entity\Show;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Performance>
 *
 * @method Performance|null find($id, $lockMode = null, $lockVersion = null)
 * @method Performance|null findOneBy(array $criteria, array $orderBy = null)
 * @method Performance[]    findAll()
 * @method Performance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PerformanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Performance::class);
    }

    public function findAvailablePerformancesForShowWithReservations(Show $show)
    {
        $dateLimit = new DateTime();
        $dateLimit->modify("-1 hours");
        return $this->createQueryBuilder('p')
            ->orderBy('p.performedAt', 'ASC')
            ->leftJoin('p.reservations', 'r')
            ->addSelect('r')
            ->where("p.performedAt > :dateLimit")
            ->setParameter('dateLimit', $dateLimit)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Performance[] Returns an array of Performance objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Performance
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
