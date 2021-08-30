<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * @param $query
     * @param $locale
     * @return QueryBuilder
     */
    public function findByQuery($query, $locale): QueryBuilder
    {
        $qb = $this->createQueryBuilder('b')
            ->leftJoin('b.bookNames', 'bn', Join::WITH, 'bn.locale = :locale')
            ->addSelect('bn')

            ->setParameter('locale', $locale);

        if ($query) {
            return $qb
                ->andWhere('LOWER(bn.name) like :query')
                ->setParameter('query', '%' . mb_strtolower($query) . '%');
        }
        return $qb;
    }

    public function findOneWithLocale($book, $locale): ?Book
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.bookNames', 'bn', Join::WITH, 'bn.locale = :locale')
            ->addSelect('bn')
            ->setParameter('locale', $locale)
            ->andWhere('b.id = :book')
            ->setParameter('book', $book)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    // /**
    //  * @return Book[] Returns an array of Book objects
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
    public function findOneBySomeField($value): ?Book
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
