<?php

namespace App\Repository;

use App\Entity\EmployeeSchedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmployeeSchedule>
 *
 * @method EmployeeSchedule|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmployeeSchedule|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmployeeSchedule[]    findAll()
 * @method EmployeeSchedule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmployeeScheduleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmployeeSchedule::class);
    }

//    /**
//     * @return EmployeeSchedule[] Returns an array of EmployeeSchedule objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EmployeeSchedule
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
