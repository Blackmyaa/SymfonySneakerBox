<?php

namespace App\Controller\admin;

use App\Entity\Categories;
use App\Repository\ProduitsRepository;
use App\Repository\CategoriesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/categorie', name: 'admin_categorie_')]
class AdminCategorieController extends AbstractController
{


    #[Route('/', name: 'index')]
    public function index(CategoriesRepository $categorieRepo): Response
    {

        $categories = $categorieRepo->findAll([], ['categoryOrder' => 'asc']);
        return $this->render('admin/categorie/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/{slug}', name: 'list')]
    public function list(Categories $categories, ProduitsRepository $produitsRepository, Request $request): Response
    {
        // On va chercher tout les produits de la catégorie

        //On utilise la fonction créée dans le repository pour afficher la quete en fixant les parametres
        $page = $request->query->getInt('page', 1);

        //Pour changer le numero de page dans l'affichage des résultat on va chercher le numéro de page dans l'url
        $produits = $produitsRepository->findProductPaginated($page, $categories->getSlug(), 12);
        //findProductPaginated(1, $categories->getSlug(), 2) (numero de page, affichage des produits, nombre de produits par page)

        $prod = $produitsRepository->findBy(['categories'=>$categories]);

        $nombreRef = 0;
        $stockTotalCat = 0;
        $prixTotalCat = 0;
        $pieces = 0;
        $prixMoyen = 0;

        foreach($prod as $produit)
        {
            //Calcul du nombre de référence
            $nombreRef = $nombreRef +1;
            //calcul du stock global de la catégorie
            $stockTotalCat = $stockTotalCat + $produit->getStock();
            
            //calcul de la valeur
            $prixTotalCat = ($prixTotalCat + $produit->getStock() * $produit->getPrix());
        }
        $prixTotalCat =  $prixTotalCat/100;
        
        if ( $stockTotalCat != 0) {
            $prixMoyen = $prixTotalCat / $stockTotalCat;
        }
        else {
            $prixMoyen = 0;
        }
        
        if ($stockTotalCat > 1) {
            $pieces = 'pièces';
        }
        else {
            $pieces = 'pièce';
        }

        return $this->render('admin/categorie/admin_categorie_liste.html.twig', [
            'categories' => $categories,
            'produits'=> $produits,
            'nombreRef'=> $nombreRef,
            'stockTotalCat'=>$stockTotalCat,
            'pieces'=>$pieces,
            'prixTotalCat'=> $prixTotalCat,
            'prixMoyen' => $prixMoyen
        ]);
    }
    
}
