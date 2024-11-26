<?php

namespace App\Repository;

use App\Entity\Commandes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commandes>
 *
 * @method Commandes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commandes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commandes[]    findAll()
 * @method Commandes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commandes::class);
    }

    //calculer le montant de chaque commande
    public function findOrderAmountByReference(string $orderReference): ?float 
    { 
        return $this->createQueryBuilder('c') 
            ->select('SUM(d.prix * d.quantite) as totalAmount') 
            ->join('c.detailCommandes', 'd') 
            ->where('c.reference = :orderReference') 
            ->setParameter('orderReference', $orderReference) 
            ->getQuery() 
            ->getSingleScalarResult();
    }


    //On trouve la derniere Commande pour recuperer ses details et les utiliser dans le dashboard
    public function findLastOrder(): ?Commandes 
    { 
        return $this->createQueryBuilder('c') 
            ->orderBy('c.created_at', 'DESC') 
            ->setMaxResults(1) 
            ->getQuery() 
            ->getOneOrNullResult(); 
    }

    // Calcul du montant de la derniere commande uniquement
    public function findLastOrderAmount(Commandes $order): ?float 
    { 
        return $this->createQueryBuilder('c') 
            ->select('SUM(d.prix * d.quantite) as totalAmount') 
            ->join('c.detailCommandes', 'd') 
            ->where('c.id = :orderId')
            ->setParameter('orderId', $order->getId()) 
            ->getQuery() 
            ->getSingleScalarResult();
    }

    //On compte le nombre de ventes effectuées le mois dernier
    public function countLastMonthSales(): int 
    { 
        $firstDayOfLastMonth = new \DateTimeImmutable('first day of last month'); 
        $lastDayOfLastMonth = new \DateTimeImmutable('last day of last month'); 
        
        return $this->createQueryBuilder('c') 
            ->select('COUNT(c.id)') 
            ->where('c.created_at BETWEEN :firstDayOfLastMonth AND :lastDayOfLastMonth') 
            ->setParameter('firstDayOfLastMonth', $firstDayOfLastMonth->format('Y-m-d 00:00:00')) 
            ->setParameter('lastDayOfLastMonth', $lastDayOfLastMonth->format('Y-m-d 23:59:59')) 
            ->getQuery() 
            ->getSingleScalarResult(); 
    }

    //On ajoute les montants de toutes kes ventes du mois dernier
    public function sumLastMonthSales(): float 
    { 
        $firstDayOfLastMonth = new \DateTimeImmutable('first day of last month'); 
        $lastDayOfLastMonth = new \DateTimeImmutable('last day of last month'); 
        
        return $this->createQueryBuilder('c') 
            ->select('SUM(d.prix * d.quantite) as totalAmount') 
            ->join('c.detailCommandes', 'd') 
            ->where('c.created_at BETWEEN :firstDayOfLastMonth AND :lastDayOfLastMonth') 
            ->setParameter('firstDayOfLastMonth', $firstDayOfLastMonth->format('Y-m-d 00:00:00')) 
            ->setParameter('lastDayOfLastMonth', $lastDayOfLastMonth->format('Y-m-d 23:59:59')) 
            ->getQuery() 
            ->getSingleScalarResult(); 
    }

    //On compte le nombre de ventes effectuées le mois dernier
    public function countThisMonthSales(): int 
    { 
        $firstDayOfthisMonth = new \DateTimeImmutable('first day of this month'); 
        $today = new \DateTimeImmutable('today'); 
        
        return $this->createQueryBuilder('c') 
            ->select('COUNT(c.id)') 
            ->where('c.created_at BETWEEN :firstDayOfThisMonth AND :lastDayOfThisMonth') 
            ->setParameter('firstDayOfThisMonth', $firstDayOfthisMonth->format('Y-m-d 00:00:00')) 
            ->setParameter('lastDayOfThisMonth', $today->format('Y-m-d 23:59:59')) 
            ->getQuery() 
            ->getSingleScalarResult(); 
    }

    //On ajoute les montants de toutes kes ventes du mois en cours
    public function sumThisMonthSales(): float 
    { 
        $firstDayOfthisMonth = new \DateTimeImmutable('first day of this month'); 
        $today = new \DateTimeImmutable('today'); 
        
        return $this->createQueryBuilder('c') 
            ->select('SUM(d.prix * d.quantite) as totalAmount') 
            ->join('c.detailCommandes', 'd') 
            ->where('c.created_at BETWEEN :firstDayOfLastMonth AND :lastDayOfLastMonth') 
            ->setParameter('firstDayOfLastMonth', $firstDayOfthisMonth->format('Y-m-d 00:00:00')) 
            ->setParameter('lastDayOfLastMonth', $today->format('Y-m-d 23:59:59')) 
            ->getQuery() 
            ->getSingleScalarResult(); 
    }
    
    // Meme logique pour la semaine en cours et la semaine précédente
    public function countLastWeekSales(): int
    {
        $firstDayOfLastWeek = new \DateTimeImmutable('last week monday');
        $lastDayOfLastWeek = new \DateTimeImmutable('last week sunday');

        return $this->createQueryBuilder('c') 
            ->select('COUNT(c.id)') 
            ->where('c.created_at BETWEEN :firstDayOfLastWeek AND :lastDayOfThisWeek') 
            ->setParameter('firstDayOfLastWeek', $firstDayOfLastWeek->format('Y-m-d 00:00:00')) 
            ->setParameter('lastDayOfThisWeek', $lastDayOfLastWeek->format('Y-m-d 23:59:59')) 
            ->getQuery() 
            ->getSingleScalarResult();
    }

    public function sumLastWeekSales(): float 
    { 
        $firstDayOfLastWeek = new \DateTimeImmutable('last week monday'); 
        $lastDayOfLastWeek = new \DateTimeImmutable('last week sunday'); 
        
        return $this->createQueryBuilder('c') 
            ->select('SUM(d.prix * d.quantite) as totalAmount') 
            ->join('c.detailCommandes', 'd') 
            ->where('c.created_at BETWEEN :firstDayOfLastWeek AND :lastDayOfLastWeek') 
            ->setParameter('firstDayOfLastWeek', $firstDayOfLastWeek->format('Y-m-d 00:00:00')) 
            ->setParameter('lastDayOfLastWeek', $lastDayOfLastWeek->format('Y-m-d 23:59:59')) 
            ->getQuery() 
            ->getSingleScalarResult(); 
    }

    public function countThisWeekSales(): int
    {
        $monday = new \DateTimeImmutable('monday this week'); 
        $today = new \DateTimeImmutable('today'); 

        return $this->createQueryBuilder('c') 
            ->select('COUNT(c.id)') 
            ->where('c.created_at BETWEEN :monday AND :today') 
            ->setParameter('monday', $monday->format('Y-m-d 00:00:00')) 
            ->setParameter('today', $today->format('Y-m-d 23:59:59')) 
            ->getQuery() 
            ->getSingleScalarResult();
    }

    public function sumThisWeekSales(): float 
    { 
        $monday = new \DateTimeImmutable('monday this week'); 
        $today = new \DateTimeImmutable('today'); 

        return $this->createQueryBuilder('c') 
            ->select('SUM(d.prix * d.quantite) as totalAmount') 
            ->join('c.detailCommandes', 'd') 
            ->where('c.created_at BETWEEN :monday AND :today') 
            ->setParameter('monday', $monday->format('Y-m-d 00:00:00')) 
            ->setParameter('today', $today->format('Y-m-d 23:59:59')) 
            ->getQuery() 
            ->getSingleScalarResult(); 
    }

    public function countTodaysSales(): int 
    { 
        $today = new \DateTimeImmutable('today'); 

        return $this->createQueryBuilder('c') 
            ->select('COUNT(c.id)') 
            ->where('c.created_at BETWEEN :todayStart AND :todayEnd') 
            ->setParameter('todayStart', $today->format('Y-m-d 00:00:00')) 
            ->setParameter('todayEnd', $today->format('Y-m-d 23:59:59')) 
            ->getQuery() 
            ->getSingleScalarResult(); 
    } 
    
    public function sumTodaysSales(): float 
    { 
        $today = new \DateTimeImmutable('today'); 
        
        return $this->createQueryBuilder('c') 
            ->select('SUM(d.prix * d.quantite) as totalAmount') 
            ->join('c.detailCommandes', 'd') 
            ->where('c.created_at BETWEEN :todayStart AND :todayEnd') 
            ->setParameter('todayStart', $today->format('Y-m-d 00:00:00')) 
            ->setParameter('todayEnd', $today->format('Y-m-d 23:59:59')) 
            ->getQuery() ->getSingleScalarResult(); 
    } 
    
    public function countYesterdaysSales(): int 
    { 
        $yesterdayStart = (new \DateTimeImmutable('yesterday'))->setTime(0, 0, 0); 
        $yesterdayEnd = (new \DateTimeImmutable('yesterday'))->setTime(23, 59, 59); 
        
        return $this->createQueryBuilder('c') 
            ->select('COUNT(c.id)') 
            ->where('c.created_at BETWEEN :yesterdayStart AND :yesterdayEnd') 
            ->setParameter('yesterdayStart', $yesterdayStart->format('Y-m-d H:i:s')) 
            ->setParameter('yesterdayEnd', $yesterdayEnd->format('Y-m-d H:i:s')) 
            ->getQuery() 
            ->getSingleScalarResult(); 
    } 
    
    public function sumYesterdaysSales(): float 
    { 
        $yesterdayStart = (new \DateTimeImmutable('yesterday'))->setTime(0, 0, 0); 
        $yesterdayEnd = (new \DateTimeImmutable('yesterday'))->setTime(23, 59, 59); 
        
        return $this->createQueryBuilder('c') 
            ->select('SUM(d.prix * d.quantite) as totalAmount') 
            ->join('c.detailCommandes', 'd') 
            ->where('c.created_at BETWEEN :yesterdayStart AND :yesterdayEnd') 
            ->setParameter('yesterdayStart', $yesterdayStart->format('Y-m-d H:i:s')) 
            ->setParameter('yesterdayEnd', $yesterdayEnd->format('Y-m-d H:i:s')) 
            ->getQuery() 
            ->getSingleScalarResult();
    }

    public function countCurrentYearSales(): int 
    { 
        $firstDayOfYear = new \DateTimeImmutable('first day of January this year'); 
        $today = new \DateTimeImmutable('today'); 
        
        return $this->createQueryBuilder('c') 
            ->select('COUNT(c.id)') 
            ->where('c.created_at BETWEEN :firstDayOfYear AND :today') 
            ->setParameter('firstDayOfYear', $firstDayOfYear->format('Y-m-d 00:00:00')) 
            ->setParameter('today', $today->format('Y-m-d 23:59:59')) 
            ->getQuery() 
            ->getSingleScalarResult(); 
    } 
    
    public function sumCurrentYearSales(): float 
    { 
        $firstDayOfYear = new \DateTimeImmutable('first day of January this year'); 
        $today = new \DateTimeImmutable('today'); 
        
        return $this->createQueryBuilder('c') 
            ->select('SUM(d.prix * d.quantite) as totalAmount') 
            ->join('c.detailCommandes', 'd') 
            ->where('c.created_at BETWEEN :firstDayOfYear AND :today') 
            ->setParameter('firstDayOfYear', $firstDayOfYear->format('Y-m-d 00:00:00')) 
            ->setParameter('today', $today->format('Y-m-d 23:59:59')) 
            ->getQuery() ->getSingleScalarResult(); 
    } 
    
    public function countLastYearSales(): int 
    { 
        $firstDayOfLastYear = new \DateTimeImmutable('first day of January last year'); 
        $lastDayOfLastYear = new \DateTimeImmutable('last day of December last year'); 

        return $this->createQueryBuilder('c') 
            ->select('COUNT(c.id)') 
            ->where('c.created_at BETWEEN :firstDayOfLastYear AND :lastDayOfLastYear') 
            ->setParameter('firstDayOfLastYear', $firstDayOfLastYear->format('Y-m-d 00:00:00')) 
            ->setParameter('lastDayOfLastYear', $lastDayOfLastYear->format('Y-m-d 23:59:59'))
            ->getQuery() ->getSingleScalarResult(); 
    } 
    
    public function sumLastYearSales(): float 
    { 
        $firstDayOfLastYear = new \DateTimeImmutable('first day of January last year'); 
        $lastDayOfLastYear = new \DateTimeImmutable('last day of December last year'); 
        
        return $this->createQueryBuilder('c') 
            ->select('SUM(d.prix * d.quantite) as totalAmount') 
            ->join('c.detailCommandes', 'd') 
            ->where('c.created_at BETWEEN :firstDayOfLastYear AND :lastDayOfLastYear') 
            ->setParameter('firstDayOfLastYear', $firstDayOfLastYear->format('Y-m-d 00:00:00')) 
            ->setParameter('lastDayOfLastYear', $lastDayOfLastYear->format('Y-m-d 23:59:59')) 
            ->getQuery() 
            ->getSingleScalarResult(); 
    } 
}