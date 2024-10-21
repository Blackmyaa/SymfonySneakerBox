<?php

namespace App\Repository;

use App\Entity\Produits;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Produits>
 *
 * @method Produits|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produits|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produits[]    findAll()
 * @method Produits[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produits::class);
    }

    //fonction qui va gerer la pagination des résultats
    public function findProductPaginated(int $page, string $slug, int $limit = 8): array // limite modifiable également dans le categoriesController
    {
        $limit = abs($limit);

        $result = [];

        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('c', 'p')
            ->from('App\Entity\Produits', 'p')
            ->join('p.categories', 'c')
            ->where("c.slug = '$slug'")
            ->setMaxResults($limit)
            ->setFirstResult(($page * $limit) - $limit);

        //pagination
        $paginator = new Paginator($query);
        $data = $paginator->getQuery()->getresult();

        //on vérifie qu'on a des données
        if(empty($data)){
            return $result;
        }
        //on calcul le nombre de page 
        $pages= ceil($paginator->count() / $limit);
        //On va remplir le tableau
        $result['data'] = $data;
        $result['pages'] =$pages;
        $result['page'] =$page;
        $result['limit'] =$limit;


        return $result;
    }

//    /**
//     * @return Produits[] Returns an array of Produits objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Produits
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
