<?php

namespace App\Repository;

use App\Entity\Contract;
use App\Entity\Show;
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
            ->where('c.fetchDataStatus = :fetchDataStatus')
            ->andWhere('o = :owner')
            ->setParameter('fetchDataStatus', Contract::FETCH_DATA_STATUS_SENT_TO_COMPANY)
            ->setParameter('owner', $user)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getUserLastCompletedContract(User $user): ?Contract
    {
        return  $this->createQueryBuilder('c')
            ->leftJoin('c.relatedProject', 'rp')
            ->leftJoin('rp.owner', 'o')
            ->where('c.fetchDataStatus = :fetchDataStatus')
            ->andWhere('o = :owner')
            ->orderBy('c.contractDate', 'DESC')
            ->setParameter('fetchDataStatus', Contract::FETCH_DATA_STATUS_FILLED_BY_COMPANY)
            ->setParameter('owner', $user)

            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function getWorkflowReadyContractsForShow(Show $show)
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.workflow', 'w')
            ->where('w IS NULL')
            ->andWhere('c.relatedProject = :related_project')
            ->setParameter('related_project', $show)
            ->getQuery()
            ->getResult();
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
