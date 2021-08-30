<?php

namespace App\Repository;

use App\Entity\BookName;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BookName|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookName|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookName[]    findAll()
 * @method BookName[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookNameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookName::class);
    }

    // /**
    //  * @return BookName[] Returns an array of BookName objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BookName
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
