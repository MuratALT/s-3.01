<?php

namespace App\Repository;

use App\Entity\LastPassword;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LastPassword>
 *
 * @method LastPassword|null find($id, $lockMode = null, $lockVersion = null)
 * @method LastPassword|null findOneBy(array $criteria, array $orderBy = null)
 * @method LastPassword[]    findAll()
 * @method LastPassword[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LastPasswordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LastPassword::class);
    }

    public function save(LastPassword $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LastPassword $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function isDifferent(string $password, LastPassword $lastPassword): bool
    {
        return $password !== $lastPassword->getPassword1() && $password !== $lastPassword->getPassword2() && $password !== $lastPassword->getPassword3();
    }

//    /**
//     * @return LastPassword[] Returns an array of LastPassword objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LastPassword
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
