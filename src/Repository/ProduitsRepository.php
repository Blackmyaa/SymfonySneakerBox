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

    public function findByQuery($query)
    {
        return $this->createQueryBuilder('p')
            ->where('p.nom LIKE :query OR p.description LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();
    }

    public function findByFilters($query, $description, $minPrice, $maxPrice)
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.nom LIKE :query')
            ->andWhere('p.description LIKE :description')
            ->setParameters([
                'query' => '%' . $query . '%',
                'description' => '%' . $description . '%',
            ]);

        if ($minPrice) {
            $qb->andWhere('p.prix/100 >= :minPrice')
                ->setParameter('minPrice', $minPrice);
        }

        if ($maxPrice) {
            $qb->andWhere('p.prix/100 <= :maxPrice')
                ->setParameter('maxPrice', $maxPrice);
        }

        return $qb->getQuery()->getResult();
    }
}