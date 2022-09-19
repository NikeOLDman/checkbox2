<?php

namespace App\Repository;

use App\Entity\Tasks;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tasks|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tasks|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tasks[]    findAll()
 * @method Tasks[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TasksRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tasks::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Tasks $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Tasks $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }


    public function findClosedTasks($userId, $orderByTask)
    {
        if ($orderByTask == 'createtime') {
            $tasks = $this->createQueryBuilder('t')
                ->where('t.user = :user')
                ->andWhere('t.checked IS NOT NULL')
                ->andWhere('t.deleted = 0')
                ->setParameter('user', $userId)
                ->orderBy('t.createtime', 'ASC');
        } elseif ($orderByTask == 'title') {
            $tasks = $this->createQueryBuilder('t')
                ->where('t.user = :user')
                ->andWhere('t.checked IS NOT NULL')
                ->andWhere('t.deleted = 0')
                ->setParameter('user', $userId)
                ->orderBy('t.title', 'ASC');
        } else {
            $tasks = $this->createQueryBuilder('t')
                ->where('t.user = :user')
                ->andWhere('t.checked IS NOT NULL')
                ->andWhere('t.deleted = 0')
                ->setParameter('user', $userId)
                ->orderBy('t.deadline', 'ASC');
        }
        $query = $tasks->getQuery();
        return $query->getResult();
    }

    public function findCountActiveTasks($userId)
    {
        return $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->where('t.user = :user')
            ->andWhere('t.checked IS NULL')
            ->andWhere('t.deleted = 0')
            ->setParameter('user', $userId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findAllCountActiveTasks()
    {
        return $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->Where('t.checked IS NULL')
            ->andWhere('t.deleted = 0')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findCountDeadlineTasks($userId, $date)
    {
        return $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->where('t.user = :user')
            ->andWhere('t.checked IS NULL')
            ->andWhere('t.deleted = 0')
            ->andWhere('t.deadline < :date')
            ->setParameter('user', $userId)
            ->setParameter('date', $date)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findCountAllDeadlineTasks($date)
    {
        return $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->Where('t.checked IS NULL')
            ->andWhere('t.deleted = 0')
            ->andWhere('t.deadline < :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getSingleScalarResult();
    }

    // /**
    //  * @return Tasks[] Returns an array of Tasks objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Tasks
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
