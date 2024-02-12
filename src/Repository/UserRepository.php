<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findEmployees(): array
//    {
//        return $this->createQueryBuilder('u')
//            ->orWhere('u.role = :val')
//            ->setParameter('val', 'ROLE_EMPLOYE' )
//            ->andWhere('u.role = :val2')
//            ->setParameter('val2', 'ROLE_MANAGER' )
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findByRoles($role)
    {
        $qb = $this->createQueryBuilder('u');

        $qb->where('u.role = :role1')
            ->setParameter('role1', 'presta2');

        return $qb->getQuery()->getResult();
    }


    public function findUsersWithRoleEmployee(): array
    {
        return $this->createQueryBuilder('u')
            ->where("JSONB_ARRAY_ELEMENTS_TEXT(u.roles)::text LIKE '%ROLE_EMPLOYEE%'")
            ->getQuery()
            ->getResult();
    }

//    public function findByRole(string $role): array
//    {
//        // The ResultSetMapping maps the SQL result to entities
//        $rsm = $this->createResultSetMappingBuilder('u');
//
//        $rawQuery = sprintf(
//            'SELECT %s
//        FROM user u
//        WHERE u.roles::jsonb ?? :role',
//            $rsm->generateSelectClause()
//        );
//
//        $query = $this->getEntityManager()->createNativeQuery($rawQuery, $rsm);
//        $query->setParameter('role', $role);
//        return $query->getResult();
//    }


    public function findByRole(string $role): array
    {
        $rsm = $this->createResultSetMappingBuilder('u');
        $rsm->addRootEntityFromClassMetadata(User::class, 'u');

        // Assurez-vous que les noms des colonnes dans la requête correspondent à ceux dans votre base de données.
        $rawQuery = sprintf(
            'SELECT %s
        FROM "user" u
        WHERE u.roles::jsonb ?? :role',
            $rsm->generateSelectClause()
        );

        $query = $this->getEntityManager()->createNativeQuery($rawQuery, $rsm);
        $query->setParameter('role', $role);
        return $query->getResult();
    }


}
