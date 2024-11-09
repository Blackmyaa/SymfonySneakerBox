<?php

namespace App\Repository;

use App\Entity\VisitCounter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VisitCounter>
 *
 * @method VisitCounter|null find($id, $lockMode = null, $lockVersion = null)
 * @method VisitCounter|null findOneBy(array $criteria, array $orderBy = null)
 * @method VisitCounter[]    findAll()
 * @method VisitCounter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VisitCounterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VisitCounter::class);
    }

    public function countVisits(\DateTime $startDate, \DateTime $endDate, $connected = null, $pageVisit = true, $loginAfterVisit = null): int 
    { 
        $qb = $this->createQueryBuilder('v') ->select('count(v.id)') ->where('v.date BETWEEN :start AND :end') ->andWhere('v.pageVisit = :pageVisit') ->setParameter('start', $startDate) ->setParameter('end', $endDate) ->setParameter('pageVisit', $pageVisit); 
        
        if ($connected !== null) { 
            $qb->andWhere('v.connected = :connected') ->setParameter('connected', $connected); 
        } 
        
        if ($loginAfterVisit !== null) { 
            $qb->andWhere('v.loginAfterVisit = :loginAfterVisit') ->setParameter('loginAfterVisit', $loginAfterVisit); 
        } 
        
        return (int) $qb->getQuery()->getSingleScalarResult();
    }
    
    public function countCurrentlyConnected(): int 
    { 
            // Implémentation spécifique pour compter les utilisateurs connectés actuellement }
    }
}
    // Implémentation spécifique pour compter les utilisateurs connectés actuellement }
    //    /**
    //     * @return VisitCounter[] Returns an array of VisitCounter objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }
    
    //    public function findOneBySomeField($value): ?VisitCounter
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
