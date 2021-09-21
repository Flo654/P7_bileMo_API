<?php

namespace App\Repository;

use App\Entity\FinalUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FinalUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method FinalUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method FinalUser[]    findAll()
 * @method FinalUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FinalUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FinalUser::class);
    }

    // /**
    //  * @return FinalUser[] Returns an array of FinalUser objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FinalUser
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
