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

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }


    public function findAllWithoutAdmin() : array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.roles NOT LIKE :role')
            ->setParameter('role', '%ROLE_ADMIN%')
            ->getQuery()
            ->getResult();
    }

    public function findAllWithoutAdminAndWait() : array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_VALID%')
            ->getQuery()
            ->getResult();
    }

    public function findAllWait() : array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.roles NOT LIKE :role')
            ->andWhere('u.roles NOT LIKE :role2')
            ->setParameter('role', '%ROLE_ADMIN%')
            ->setParameter('role2', '%ROLE_VALID%')
            ->getQuery()
            ->getResult();
    }

    function getNumberUserWait() : int
    {
        return count($this->findAllWait());
    }


    function findAllReferant() : array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :role')
            ->andWhere('u.fonction LIKE :fonction')
            ->setParameter('role', '%ROLE_VALID%')
            ->setParameter('fonction', '%Referant%')
            ->getQuery()
            ->getResult();
    }

    function findAllWithoutMe(User $user) : array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.id != :id')
            ->setParameter('id', $user->getId())
            ->getQuery()
            ->getResult();
    }

    public function findAllWaitMoreThanOneDay() : array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.roles NOT LIKE :role')
            ->andWhere('u.roles NOT LIKE :role2')
            ->andWhere('u.creationDate < :date')
            ->setParameter('role', '%ROLE_ADMIN%')
            ->setParameter('role2', '%ROLE_VALID%')
            ->setParameter('date', new \DateTime('-1 day'))
            ->getQuery()
            ->getResult();
    }

    public function findAllAdmin() : array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_ADMIN%')
            ->getQuery()
            ->getResult();
    }

    public function findBySearch($user = null, $service = null, $fonction = null , $archive = false) : array
    {
        $query = $this->createQueryBuilder('u')
            ->andWhere('u.roles NOT LIKE :role')
            ->setParameter('role', '%ROLE_ADMIN%');

        if ($user != null) {
            $query->andWhere('u.nom LIKE :user')
                ->orWhere('u.prenom LIKE :user')
                ->setParameter('user', '%'.$user.'%');
        }

        if ($service != null) {
            $query->leftJoin('u.service', 's')
                ->andWhere('s.id = :service')
                ->setParameter('service', $service);
        }
        if ($fonction != null) {
            $query->leftJoin('u.fonction', 'f')
                ->andWhere('f.id = :fonction')
                ->setParameter('fonction', $fonction);
        }

        if ($archive) {
            $query->andWhere('u.is_archive = :archive')
                ->setParameter('archive', true);
        } else {
            $query->andWhere('u.is_archive = :archive')
                ->setParameter('archive', false);
        }

        return $query->getQuery()
            ->getResult();


    }
    public function ListAllReferent($serviceID) : array
    {
       $query = $this->createQueryBuilder('u')
            ->Where('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_VALID%')
            ->leftJoin('u.fonction', 'f')
            ->andWhere('f.libelle LIKE :fonction')
            ->leftJoin('u.service', 's')
            ->andWhere('s.id = :service')
            ->setParameter('fonction', '%Referent%')
            ->setParameter('service', $serviceID);

        return $query->getQuery()
            ->getResult();
    }

    function findAllWithoutMeAndAdminService(int $user, string $service) : array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.id != :id')
            ->andWhere('u.roles NOT LIKE :role')
            ->setParameter('id', $user)
            ->setParameter('role', '%ROLE_ADMIN%')
            ->leftJoin('u.service', 's')
            ->andWhere('s.libelle = :service')
            ->setParameter('service', $service)
            ->getQuery()
            ->getResult();
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

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
