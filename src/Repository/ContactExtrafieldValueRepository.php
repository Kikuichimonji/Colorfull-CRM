<?php

namespace App\Repository;

use App\Entity\ContactExtrafieldValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ContactExtrafieldValue|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactExtrafieldValue|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactExtrafieldValue[]    findAll()
 * @method ContactExtrafieldValue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactExtrafieldValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactExtrafieldValue::class);
    }

    // /**
    //  * @return ContactExtrafieldValue[] Returns an array of ContactExtrafieldValue objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ContactExtrafieldValue
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
