<?php

namespace App\Repository;

use App\Entity\DetailCommande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Connection;

/**
 * @extends ServiceEntityRepository<DetailCommande>
 *
 * @method DetailCommande|null find($id, $lockMode = null, $lockVersion = null)
 * @method DetailCommande|null findOneBy(array $criteria, array $orderBy = null)
 * @method DetailCommande[]    findAll()
 * @method DetailCommande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DetailCommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, Connection $connection)
    {
        parent::__construct($registry, DetailCommande::class);
        $this->connection = $connection;
    }

    public function findMostSoldProduct(\DateTimeInterface $startDate, \DateTimeInterface $endDate): ?array 
    { 
        return $this->createQueryBuilder('d') 
            ->select('p.id, p.nom, p.stock, SUM(d.quantite) as totalQuantity') 
            ->join('d.produit', 'p') 
            ->join('d.commande', 'c') 
            ->where('c.created_at BETWEEN :startDate AND :endDate') 
            ->setParameter('startDate', $startDate->format('Y-m-d 00:00:00')) 
            ->setParameter('endDate', $endDate->format('Y-m-d 23:59:59')) 
            ->groupBy('p.id') 
            ->orderBy('totalQuantity', 'DESC') 
            ->setMaxResults(1) 
            ->getQuery() 
            ->getOneOrNullResult(); 
    }

    public function findTop5SoldProducts(\DateTimeInterface $startDate, \DateTimeInterface $endDate, ?int $categoryId = null): array 
    { 
        $qb = $this->createQueryBuilder('d') 
            ->select('p.id, p.nom, p.stock, p.slug, SUM(d.quantite) as totalQuantity') 
            ->join('d.produit', 'p') 
            ->join('d.commande', 'c') 
            ->where('c.created_at BETWEEN :startDate AND :endDate') 
            ->setParameter('startDate', $startDate->format('Y-m-d 00:00:00')) 
            ->setParameter('endDate', $endDate->format('Y-m-d 23:59:59')) 
            ->groupBy('p.id') 
            ->orderBy('totalQuantity', 'DESC') 
            ->setMaxResults(5); 
            
        if ($categoryId !== null) { 
            $qb->join('p.categories', 'cat') 
                ->andWhere('cat.id = :categoryId') 
                ->setParameter('categoryId', $categoryId); 
        } 
        return $qb->getQuery()->getResult(); 
    }

    public function findFlop5SoldProducts(\DateTimeInterface $startDate, \DateTimeInterface $endDate, ?int $categoryId = null): array 
    { 
        $qb = $this->_em->createQueryBuilder();        
        $qb->select('p.id, p.nom, p.stock, p.slug, COALESCE(SUM(d.quantite), 0) as totalQuantity') 
            ->from('App\Entity\Produits', 'p') 
            ->leftJoin('p.detailCommandes', 'd')
            ->leftJoin('d.commande', 'c', 'WITH', 'c.created_at BETWEEN :startDate AND :endDate') 
            ->setParameter('startDate', $startDate->format('Y-m-d 00:00:00'))
            ->setParameter('endDate', $endDate->format('Y-m-d 23:59:59')) 
            ->groupBy('p.id') 
            ->orderBy('totalQuantity', 'ASC') 
            ->setMaxResults(5); 
            
        if ($categoryId !== null) { 
            $qb->join('p.categories', 'cat') 
                ->andWhere('cat.id = :categoryId') 
                ->setParameter('categoryId', $categoryId); 
        } 
        return $qb->getQuery()->getResult(); 
    }

    public function findLeastSoldProduct(\DateTimeInterface $startDate, \DateTimeInterface $endDate, ?int $categoryId = null): ?array 
    { 
        $qb = $this->_em->createQueryBuilder(); 
        
        $qb->select('p.id, p.nom, p.stock, COALESCE(SUM(d.quantite), 0) as totalQuantity') 
        ->from('App\Entity\Produits', 'p')
        ->leftJoin('p.detailCommandes', 'd') 
        ->leftJoin('d.commande', 'c', 'WITH', 'c.created_at BETWEEN :startDate AND :endDate') 
        ->setParameter('startDate', $startDate->format('Y-m-d 00:00:00')) 
        ->setParameter('endDate', $endDate->format('Y-m-d 23:59:59')) 
        ->groupBy('p.id') 
        ->orderBy('totalQuantity', 'ASC') 
        ->addOrderBy('p.stock', 'DESC') // Sort by stock descending in case of equal sales 
        ->setMaxResults(1); // We only want the least sold product 
        
        if ($categoryId !== null) { 
            $qb->leftJoin('p.categories', 'cat')
            ->andWhere('cat.id = :categoryId') 
            ->setParameter('categoryId', $categoryId); 
        }
        
        return $qb->getQuery()->getOneOrNullResult(); 
    }

    //Fonction qui cherche le jour ou il y a eut le plus de vente en nombre et en chiffre
    public function findSalesAnalysis(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array 
    { 
        // Query for the day with the most sales 
        $qbMostSales = $this->_em->createQueryBuilder(); 
        $qbMostSales->select('c.registeredAt as saleDay, COUNT(d.quantite) as totalSales') 
            ->from('App\Entity\DetailCommande', 'd') 
            ->join('d.commande', 'c') 
            ->where('c.registeredAt BETWEEN :startDate AND :endDate') 
            ->setParameter('startDate', $startDate->format('Y-m-d 00:00:00')) 
            ->setParameter('endDate', $endDate->format('Y-m-d 23:59:59')) 
            ->groupBy('saleDay') 
            ->orderBy('totalSales', 'DESC') 
            ->setMaxResults(1); 
            
        $dayWithMostSales = $qbMostSales->getQuery()->getOneOrNullResult(); 
        
        // Query for the day with the highest sales amount 
        $qbHighestAmount = $this->_em->createQueryBuilder(); 
        $qbHighestAmount->select('c.registeredAt as saleDay, SUM(d.quantite * p.prix) as totalAmount') 
            ->from('App\Entity\DetailCommande', 'd') 
            ->join('d.commande', 'c') 
            ->join('d.produit', 'p') 
            ->where('c.registeredAt BETWEEN :startDate AND :endDate') 
            ->setParameter('startDate', $startDate->format('Y-m-d 00:00:00')) 
            ->setParameter('endDate', $endDate->format('Y-m-d 23:59:59')) 
            ->groupBy('saleDay') ->orderBy('totalAmount', 'DESC') 
            ->setMaxResults(1); 
            
        $dayWithHighestSalesAmount = $qbHighestAmount->getQuery()->getOneOrNullResult(); 
        
        $qbTotalSales = $this->_em->createQueryBuilder(); 
        $qbTotalSales->select('SUM(d.quantite * p.prix) as totalSalesPeriod') 
        ->from('App\Entity\DetailCommande', 'd') 
        ->join('d.commande', 'c') 
        ->join('d.produit', 'p') 
        ->where('c.registeredAt BETWEEN :startDate AND :endDate') 
        ->setParameter('startDate', $startDate->format('Y-m-d 00:00:00')) 
        ->setParameter('endDate', $endDate->format('Y-m-d 23:59:59')); 
        
        $totalSalesPeriod = $qbTotalSales->getQuery()->getSingleScalarResult();

        return [ 
            'dayWithMostSales' => $dayWithMostSales, 
            'dayWithHighestSalesAmount' => $dayWithHighestSalesAmount,
            'totalSalesPeriod' => $totalSalesPeriod ? (float) $totalSalesPeriod : null, 
        ]; 
    }

    public function findTotalSalesAmountForDay(\DateTimeInterface $date): ?float 
    { 
        $qb = $this->_em->createQueryBuilder(); 
        $qb->select('SUM(d.quantite * p.prix) as totalAmount') 
            ->from('App\Entity\DetailCommande', 'd') 
            ->join('d.commande', 'c') 
            ->join('d.produit', 'p') 
            ->where('c.registeredAt = :date') 
            ->setParameter('date', $date->format('Y-m-d 00:00:00')); 
        
        $result = $qb->getQuery()->getSingleScalarResult(); 
        return $result ? (float) $result : null;
    }
}
