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
        // RECHERCHE POUR UN SEUL MOT DANS LA ZONE DE RECHERCHE
        // return $this->createQueryBuilder('p')
        //     ->where('p.nom LIKE :query OR p.description LIKE :query')
        //     ->setParameter('query', '%' . $query . '%')
        //     ->getQuery()
        //     ->getResult();

        //Methode pour pouvoir taper plusieurs nombre et mots dans un seul input 
        {
            $qb = $this->createQueryBuilder('p');
    
            // Vérifier si l'input contient un ou deux chiffres
            $pattern = '/\d+/';
            preg_match_all($pattern, $query, $matches);
            if (count($matches[0]) >= 2) {
                $numbers = array_map('intval', $matches[0]);
                sort($numbers); // Trier les chiffres pour obtenir le plus petit et le plus grand
                $minPrice = $numbers[0];
                $maxPrice = $numbers[1];
            } elseif (count($matches[0]) == 1) {
                $maxPrice = intval($matches[0][0]); // Utiliser le seul chiffre trouvé comme prix maximum
                $minPrice = null;
            } else {
                $minPrice = null;
                $maxPrice = null;
            }
    
            $query = preg_replace($pattern, '', $query); // Supprimer les chiffres de l'input pour traiter les mots restants
    
            // Diviser l'input en mots individuels
            $mots = explode(' ', trim($query));
            $conditions = [];
            foreach ($mots as $index => $mot) {
                if (!empty($mot)) {
                    $paramName = 'mot' . $index;
                    $conditions[] = "(p.nom LIKE :$paramName OR p.description LIKE :$paramName)";
                    $qb->setParameter($paramName, '%' . $mot . '%');
                }
            }
            
            if ($conditions) {
                $queryString = implode(' AND ', $conditions);
                $qb->where($queryString);
            }
            
            // Ajouter les filtres de prix, si disponibles
            if ($minPrice !== null) {
                $qb->andWhere('p.prix/100 >= :minPrice')
                    ->setParameter('minPrice', $minPrice);
            }
            if ($maxPrice !== null) {
                $qb->andWhere('p.prix/100 <= :maxPrice')
                    ->setParameter('maxPrice', $maxPrice);
            }
    
            return $qb->getQuery()->getResult();
        }
    }

    public function findByFilters($query, $description, $minPrice, $maxPrice)
    {
        $qb = $this->createQueryBuilder('p');

        // Traiter le champ 'query'
        $motsQuery = explode(' ', $query);
        $conditionsQuery = [];
        foreach ($motsQuery as $index => $mot) {
            $paramName = 'motQuery' . $index;
            $conditionsQuery[] = "p.nom LIKE :$paramName";
            $qb->setParameter($paramName, '%' . $mot . '%');
        }
        $queryStringQuery = implode(' AND ', $conditionsQuery);

        // Traiter le champ 'description'
        $motsDescription = explode(' ', $description);
        $conditionsDescription = [];
        foreach ($motsDescription as $index => $mot) {
            $paramName = 'motDescription' . $index;
            $conditionsDescription[] = "p.description LIKE :$paramName";
            $qb->setParameter($paramName, '%' . $mot . '%');
        }
        $queryStringDescription = implode(' AND ', $conditionsDescription);

        // Construire la requête finale
        $qb->where($queryStringQuery)
            ->andWhere($queryStringDescription);

        // // UTILE dans le cas ou il y aurait plus de parametres à gérer
        // Construire la requête finale avec les poids (certains params ont plus de poids que d'autres et donnent du coup des résultats de recherche plus précis)
        // $qb->select('p,
        //             (CASE WHEN ' . $queryStringQuery . ' THEN 3 ELSE 0 END + 
        //             CASE WHEN ' . $queryStringDescription . ' THEN 1 ELSE 0 END) AS HIDDEN score')
        //         ->where($queryStringQuery . ' OR ' . $queryStringDescription)
        //         ->orderBy('score', 'DESC');

        // Ajouter les filtres de prix, si disponibles
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