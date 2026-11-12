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
            ->where("p.performedAt > :dateLimit and p.relatedProject = :show")
            ->setParameter('dateLimit', $dateLimit)
            ->setParameter('show', $show)
            ->getQuery()
            ->getResult();
    }

    /**
     * Performances with the number of seats reserved on them, for the Quotas API.
     *
     * @param list<int> $ids  restrict to these performances (all when empty)
     * @param int|null $showId restrict to this show
     * @param \DateTimeImmutable|null $from wall-clock time, as stored in performedAt
     *
     * @return list<array{id: int, projectId: int, projectName: string, performedAt: \DateTimeImmutable, quota: int, reserved: int}>
     */
    public function findWithReservedCount(array $ids = [], ?int $showId = null, ?\DateTimeImmutable $from = null, int $limit = 500): array
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p.id AS id', 'pr.id AS projectId', 'pr.name AS projectName', 'p.performedAt AS performedAt', 'p.quota AS quota', 'COALESCE(SUM(r.placeCount), 0) AS reserved')
            ->join('p.relatedProject', 'pr')
            ->leftJoin('p.reservations', 'r')
            ->groupBy('p.id', 'pr.id', 'pr.name', 'p.performedAt', 'p.quota')
            ->orderBy('p.performedAt', 'ASC')
            ->addOrderBy('p.id', 'ASC')
            ->setMaxResults($limit);

        if ([] !== $ids) {
            $qb->andWhere('p.id IN (:ids)')->setParameter('ids', $ids);
        }
        if (null !== $showId) {
            $qb->andWhere('pr.id = :show')->setParameter('show', $showId);
        }
        if (null !== $from) {
            $qb->andWhere('p.performedAt >= :from')->setParameter('from', $from);
        }

        return array_map(static fn (array $row): array => [
            'id' => (int) $row['id'],
            'projectId' => (int) $row['projectId'],
            'projectName' => (string) $row['projectName'],
            'performedAt' => $row['performedAt'],
            'quota' => (int) $row['quota'],
            'reserved' => (int) $row['reserved'],
        ], $qb->getQuery()->getArrayResult());
    }

    public function availablePerformancesQueryBuilder()
    {
        $dateLimit = new DateTime();
        $dateLimit->modify("-1 hours");
        return $this->createQueryBuilder('p')
            ->orderBy('p.performedAt', 'ASC')
            ->where("p.performedAt > :dateLimit")
            ->setParameter('dateLimit', $dateLimit)
        ;
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
