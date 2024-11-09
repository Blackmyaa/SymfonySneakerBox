<?php

namespace App\Controller\admin;

use App\Entity\Categories;
use App\Form\CategorieFormType;
use App\Form\CategorieParentFormType;
use App\Repository\ProduitsRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
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

    #[Route('/ajouter', name: 'ajout_categorie')]
    public function ajouterCategorie(CategoriesRepository $categorieRepo, Request $request, EntityManagerInterface $manager, SluggerInterface $slugger)
    {
      //On ajoute une catégorie Parent
        $categParent = new Categories();
        $formCategorieParent = $this->createForm(CategorieParentFormType::class, $categParent);
        
        //On traite la requete
        $formCategorieParent->handleRequest($request);

        //On vérifie si le formulaire est soumis et valide
        if ($formCategorieParent->isSubmitted() && $formCategorieParent->isValid()) {
            
            $nomCategorieParent = $categParent->getNom();
            // Transforme le texte en minuscules avant de créer le slug
            $nomCategorieParent = mb_strtolower($nomCategorieParent, 'UTF-8');
            //On genere le slug et on setParent=NUll
            $slug = $slugger->slug($nomCategorieParent);
            $categParent->setSlug($slug);
            $categParent->setParent(NULL);
    
            //On récupére la valeur du dernier category Order
            $lastCategorieParent = $categorieRepo->findOneBy([],['categoryOrder'=>'DESC']);
            $lastCategorieParentOrder = $lastCategorieParent->getCategoryOrder();
    
            //On lui attribue sa categoryOrder
            $categoryParentOrder = $lastCategorieParentOrder + 1;
            $categParent->setCategoryOrder($categoryParentOrder);
            
            $manager->persist($categParent);
    
            //On décale d'un rang toutes les categoryOrder qui suivront
            //On commence Par lister toutes les categories dont le CategoryOrder est supérieur à celui qu'on vient de créer
            $categP = $categorieRepo->createQueryBuilder('c')
            ->where('c.categoryOrder > :order')
            ->setParameter('order', $categoryParentOrder-1)
            ->getQuery()
            ->getResult(); 
    
            //Et pour chacune de ces categories On décale d'un rang toutes les categoryOrder
    
            foreach ($categP as $categorie) {
                $categorie->setCategoryOrder($categorie->getCategoryOrder() +1);
                $manager->persist($categorie);
            }
    
            $manager->flush();
    
            //On affiche un message pour confirmer l'ajout du produit
            $this->addFlash('success', 'Catégorie Parent créée avec succès');
    
            //On redirige vers la page de gestion des catégories
            return $this->redirectToRoute('admin_categorie_index');
    
        }

      //On ajoute une catégorie
        $categorie = new Categories();
        $formCategorie = $this->createForm(CategorieFormType::class, $categorie);

        //On traite la requete
        $formCategorie->handleRequest($request);
        
        //On vérifie si le formulaire est soumis et valide
        if ($formCategorie->isSubmitted() && $formCategorie->isValid()) {

            $nomCategorie = $categorie->getNom();
            // Transforme le texte en minuscules avant de créer le slug
            $nomCategorie = mb_strtolower($nomCategorie, 'UTF-8');
            //On genere le slug
            $slug = $slugger->slug($nomCategorie);
            $categorie->setSlug($slug);

            //On récupére la valeur du dernier category Order ayant le meme parent que la catégorie qu'on souhaite créer
            $parentId = $categorie->getParent()->getId();
            $parentNom = $categorie->getParent()->getNom();
            
            $lastCategorie = $categorieRepo->findOneBy(['parent'=>$parentId],['categoryOrder'=>'DESC']);
            
            //Si la categorie qu'on veut rajouter est la premiere d'une categorie Parent
            if ($lastCategorie == Null) {
                $lastCategorie = $categorieRepo->findOneBy(['nom'=>$parentNom],['categoryOrder'=>'DESC']);
            }

            $lastCategorieOrder = $lastCategorie->getCategoryOrder();

            //On lui attribue sa categoryOrder
            $categoryOrder = $lastCategorieOrder + 1;
            $categorie->setCategoryOrder($categoryOrder);
            
            $manager->persist($categorie);

            //On décale d'un rang toutes les categoryOrder
            //On commence Par lister toutes les categories dont le CategoryOrder est supérieur à celui qu'on vient de créer
            $categ = $categorieRepo->createQueryBuilder('c')
            ->where('c.categoryOrder > :order')
            ->setParameter('order', $categoryOrder-1)
            ->getQuery()
            ->getResult(); 

            //Et pour chacune de ces categories On décale d'un rang toutes les categoryOrder
            foreach ($categ as $categorie) {
                $categorie->setCategoryOrder($categorie->getCategoryOrder() +1);
                $manager->persist($categorie);
            }

            $manager->flush();

            //On affiche un message pour confirmer l'ajout du produit
            $this->addFlash('success', 'Catégorie créée avec succès');

            //On redirige vers la page de gestion des catégories
            return $this->redirectToRoute('admin_categorie_index');

        }

        return $this->render('admin/categorie/ajouterCategorie.html.twig',[
            'formCategorie' => $formCategorie,
            'formCategorieParent' => $formCategorieParent

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
