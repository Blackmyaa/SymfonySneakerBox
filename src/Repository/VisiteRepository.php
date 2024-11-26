<?php

namespace App\Repository;

use App\Entity\Visite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository; 
use Doctrine\Persistence\ManagerRegistry; 
use Doctrine\ORM\NonUniqueResultException;
/**
 * @extends ServiceEntityRepository<Visite>
 *
 * @method Visite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Visite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Visite[]    findAll()
 * @method Visite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VisiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Visite::class);
    }

    public function findTodaysVisits(): int 
    { 
        $today = (new \DateTimeImmutable('today'))->format('Y-m-d'); 
        return $this->createQueryBuilder('v')
            ->select('COUNT(v.id)')
            ->where('v.visitedAt = :today') 
            ->setParameter('today', $today) 
            ->getQuery() 
            ->getSingleScalarResult(); 
    }

    public function findYesterdaysVisits(): int
    { 
        $yesterday = (new \DateTimeImmutable('yesterday'))->format('Y-m-d'); 
        return $this->createQueryBuilder('v') 
            ->select('COUNT(v.id)') 
            ->where('v.visitedAt = :yesterday') 
            ->setParameter('yesterday', $yesterday) 
            ->getQuery() 
            ->getSingleScalarResult(); 
    }

    public function findCurrentWeekVisitsCount(): int 
    { 
        // Trouver le lundi de la semaine en cours 
        $monday = new \DateTimeImmutable('monday this week'); 
        $today = new \DateTimeImmutable('today'); 
        return $this->createQueryBuilder('v') 
            ->select('COUNT(v.id)') 
            ->where('v.visitedAt BETWEEN :monday AND :today') 
            ->setParameter('monday', $monday->format('Y-m-d')) 
            ->setParameter('today', $today->format('Y-m-d')) 
            ->getQuery() 
            ->getSingleScalarResult(); 
    }

    public function findLastWeekVisitsCount(): int 
    {
         // Trouver le lundi et le dimanche de la semaine dernière 
        $lastMonday = new \DateTimeImmutable('last week monday'); 
        $lastSunday = new \DateTimeImmutable('last week sunday');
        return $this->createQueryBuilder('v') 
            ->select('COUNT(v.id)') 
            ->where('v.visitedAt BETWEEN :lastMonday AND :lastSunday') 
            ->setParameter('lastMonday', $lastMonday->format('Y-m-d')) 
            ->setParameter('lastSunday', $lastSunday->format('Y-m-d')) 
            ->getQuery() 
            ->getSingleScalarResult(); 
    }

    public function findCurrentMonthVisitsCount(): int 
    { 
        $firstDayOfMonth = new \DateTimeImmutable('first day of this month'); 
        $today = new \DateTimeImmutable('today'); 
        return $this->createQueryBuilder('v') 
            ->select('COUNT(v.id)') 
            ->where('v.visitedAt BETWEEN :firstDayOfMonth AND :today') 
            ->setParameter('firstDayOfMonth', $firstDayOfMonth->format('Y-m-d')) 
            ->setParameter('today', $today->format('Y-m-d')) 
            ->getQuery() 
            ->getSingleScalarResult(); 
    } 
    
    public function findLastMonthVisitsCount(): int 
    { 
        $firstDayOfLastMonth = new \DateTimeImmutable('first day of last month'); 
        $lastDayOfLastMonth = new \DateTimeImmutable('last day of last month'); 
        return $this->createQueryBuilder('v') 
            ->select('COUNT(v.id)') 
            ->where('v.visitedAt BETWEEN :firstDayOfLastMonth AND :lastDayOfLastMonth') 
            ->setParameter('firstDayOfLastMonth', $firstDayOfLastMonth->format('Y-m-d')) 
            ->setParameter('lastDayOfLastMonth', $lastDayOfLastMonth->format('Y-m-d')) 
            ->getQuery() 
            ->getSingleScalarResult(); 
    }

    public function findCurrentYearVisitsCount(): int 
    { 
        $firstDayOfYear = new \DateTimeImmutable('first day of January this year'); 
        $today = new \DateTimeImmutable('today'); 
        return $this->createQueryBuilder('v') 
            ->select('COUNT(v.id)') 
            ->where('v.visitedAt BETWEEN :firstDayOfYear AND :today') 
            ->setParameter('firstDayOfYear', $firstDayOfYear->format('Y-m-d')) 
            ->setParameter('today', $today->format('Y-m-d')) 
            ->getQuery() 
            ->getSingleScalarResult(); 
    } 
    
    public function findLastYearVisitsCount(): int 
    { 
        $firstDayOfLastYear = new \DateTimeImmutable('first day of January last year'); 
        $lastDayOfLastYear = new \DateTimeImmutable('last day of December last year');
        return $this->createQueryBuilder('v') 
            ->select('COUNT(v.id)') 
            ->where('v.visitedAt BETWEEN :firstDayOfLastYear AND :lastDayOfLastYear') 
            ->setParameter('firstDayOfLastYear', $firstDayOfLastYear->format('Y-m-d')) 
            ->setParameter('lastDayOfLastYear', $lastDayOfLastYear->format('Y-m-d')) 
            ->getQuery() 
            ->getSingleScalarResult(); 
    }

    public function findDaysWithMostVisits(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array 
    {
        $qb = $this->_em->createQueryBuilder(); 
        $qb->select('v.visitedAt as visitDay, COUNT(v.id) as totalVisits')
            ->from('App\Entity\Visite', 'v') 
            ->where('v.visitedAt BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate->format('Y-m-d 00:00:00'))
            ->setParameter('endDate', $endDate->format('Y-m-d 23:59:59')) 
            ->groupBy('v.visitedAt') 
            ->orderBy('totalVisits', 'DESC')
            ->setMaxResults(1); // Limiter les résultats à 5
        
        return $qb->getQuery()->getResult();
    }

    public function findPeriodStatistics(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array 
    { 
        // Compter le nombre de visites 
        $qbVisits = $this->_em->createQueryBuilder(); 
        $qbVisits->select('COUNT(v.id) as totalVisits') 
            ->from('App\Entity\Visite', 'v') 
            ->where('v.visitedAt BETWEEN :startDate AND :endDate') 
            ->setParameter('startDate', $startDate->format('Y-m-d 00:00:00')) 
            ->setParameter('endDate', $endDate->format('Y-m-d 23:59:59')); 
        
            $totalVisits = $qbVisits->getQuery()->getSingleScalarResult(); 
        
        // Compter le nombre de commandes passées 
        $qbOrders = $this->_em->createQueryBuilder(); 
        $qbOrders->select('COUNT(c.id) as totalOrders') 
            ->from('App\Entity\Commandes', 'c') 
            ->where('c.registeredAt BETWEEN :startDate AND :endDate') 
            ->setParameter('startDate', $startDate->format('Y-m-d 00:00:00')) 
            ->setParameter('endDate', $endDate->format('Y-m-d 23:59:59')); 
        
        $totalOrders = $qbOrders->getQuery()->getSingleScalarResult(); 
        
        // Compter le nombre d'articles vendus 
        $qbItems = $this->_em->createQueryBuilder(); 
        $qbItems->select('SUM(d.quantite) as totalItems') 
        ->from('App\Entity\DetailCommande', 'd') 
        ->join('d.commande', 'c') 
        ->where('c.registeredAt BETWEEN :startDate AND :endDate') 
        ->setParameter('startDate', $startDate->format('Y-m-d 00:00:00')) 
        ->setParameter('endDate', $endDate->format('Y-m-d 23:59:59')); 
        
        $totalItems = $qbItems->getQuery()->getSingleScalarResult(); 
        
        return [ 
            'totalVisits' => $totalVisits ? (int) $totalVisits : 0, 
            'totalOrders' => $totalOrders ? (int) $totalOrders : 0, 
            'totalItems' => $totalItems ? (int) $totalItems : 0, 
        ]; 
    }
}
