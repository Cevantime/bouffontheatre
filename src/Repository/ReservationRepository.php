<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 *
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    /**
     * Who booked how many seats, for the Quotas API. No e-mail, no comment: only what the door list needs.
     *
     * @param list<int> $performanceIds
     *
     * @return array<int, list<array{id: int, firstName: string, lastName: string, places: int, price: string}>> by performance id
     */
    public function findGuestsByPerformance(array $performanceIds): array
    {
        if ([] === $performanceIds) {
            return [];
        }

        $rows = $this->createQueryBuilder('r')
            ->select('r.id AS id', 'IDENTITY(r.performance) AS performanceId', 'r.firstName AS firstName', 'r.lastName AS lastName', 'r.placeCount AS places', 'r.price AS price')
            ->where('r.performance IN (:ids)')
            ->setParameter('ids', $performanceIds)
            ->orderBy('r.id', 'ASC')
            ->getQuery()
            ->getArrayResult();

        $guests = [];
        foreach ($rows as $row) {
            $guests[(int) $row['performanceId']][] = [
                'id' => (int) $row['id'],
                'firstName' => (string) $row['firstName'],
                'lastName' => (string) $row['lastName'],
                'places' => (int) $row['places'],
                'price' => Reservation::AVAILABLE_PRICES[$row['price']] ?? '',
            ];
        }

        return $guests;
    }

//    /**
//     * @return Reservation[] Returns an array of Reservation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Reservation
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
