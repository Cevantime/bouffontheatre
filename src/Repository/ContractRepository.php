<?php

namespace App\Repository;

use App\Entity\Contract;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Contract>
 *
 * @method Contract|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contract|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contract[]    findAll()
 * @method Contract[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContractRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contract::class);
    }

    public function getUserContractsToComplete(User $user)
    {
        return  $this->createQueryBuilder('c')
            ->leftJoin('c.relatedProject', 'rp')
            ->leftJoin('rp.owner', 'o')
            ->where('c.status = :status')
            ->andWhere('o = :owner')
            ->setParameter('status', Contract::STATUS_SENT_TO_COMPANY)
            ->setParameter('owner', $user)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getUserLastCompletedContract(User $user)
    {
        return  $this->createQueryBuilder('c')
            ->leftJoin('c.relatedProject', 'rp')
            ->leftJoin('rp.owner', 'o')
            ->where('c.status = :status')
            ->andWhere('o = :owner')
            ->orderBy('c.contractDate', 'DESC')
            ->setParameter('status', Contract::STATUS_FILLED_BY_COMPANY)
            ->setParameter('owner', $user)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

//    /**
//     * @return Contract[] Returns an array of Contract objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Contract
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
