<?php

namespace App\Repository;

use App\Entity\ArticleItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ArticleItem>
 *
 * @method ArticleItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticleItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticleItem[]    findAll()
 * @method ArticleItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticleItem::class);
    }

//    /**
//     * @return ArticleItem[] Returns an array of ArticleItem objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ArticleItem
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
