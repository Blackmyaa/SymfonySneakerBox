<?php

namespace App\Controller\admin;

use App\Entity\Produits;
use App\Form\ProduitsFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/produits', name: 'admin_products_')]
class AdminProduitController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('admin/produit/index.html.twig', [
            'controller_name' => 'AdminProduitController',
        ]);


    }#[Route('/ajout', name: 'add')]
    public function add(Request $request, EntityManagerInterface $manager, SluggerInterface $slugger): Response
    {
        //On crée un "nouveau produit"
        $produit = new Produits();

        //On crée le formulaire
        $formProduit = $this->createForm(ProduitsFormType::class, $produit);

        //On traite la requete
        $formProduit->handleRequest($request);

        //On vérifie si le formulaire est soumis ET Valide
        if($formProduit->isSubmitted() && $formProduit->isValid()){
            //On genere ke slug
            $slug = $slugger->slug($produit->getNom());
            $produit->setSlug($slug);

            //On arrondit le prix en le mutltipliant par 100
            $prix = $produit->getPrix()*100;
            $produit->setPrix($prix);

            $manager->persist($produit);
            $manager->flush();

            //On affiche un message pour confirmer l'ajout du produit
            $this->addFlash('success', 'Produit créé avec succès');

            //On redirige vers la page de gestion des produits
            return $this->redirectToRoute('admin_products_index');

        }
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/produit/ajouter.html.twig', [
            'controller_name' => 'AdminProduitController',
            'formProduit'=>$formProduit->createView(),
        ]);
    }
    
    #[Route('/edition/{id}', name: 'edit')]
    public function edit(Produits $produit, Request $request, EntityManagerInterface $manager, SluggerInterface $slugger): Response
    {
        //On vérifie si l'utilisateur peut editer avec le voteur
        $this->denyAccessUnlessGranted('PRODUCT_EDIT', $produit);

        //On divise le prix par 100
        $prix = $produit->getPrix() / 100;
        $produit->setPrix($prix);


        //On crée le formulaire
        $formProduit = $this->createForm(ProduitsFormType::class, $produit);

        //On traite la requete
        $formProduit->handleRequest($request);

        //On vérifie si le formulaire est soumis ET Valide
        if($formProduit->isSubmitted() && $formProduit->isValid()){
            //On genere ke slug
            $slug = $slugger->slug($produit->getNom());
            $produit->setSlug($slug);

            //On arrondit le prix en le mutltipliant par 100
            $prix = $produit->getPrix()*100;
            $produit->setPrix($prix);

            $manager->persist($produit);
            $manager->flush();

            //On affiche un message pour confirmer l'ajout du produit
            $this->addFlash('success', 'Produit modifié avec succès');

            //On redirige vers la page de gestion des produits
            return $this->redirectToRoute('admin_products_index');
        }
        return $this->render('admin/produit/edition.html.twig', [
            'produit' => $produit,
            'formProduit'=>$formProduit->createView(),
        ]);
    }
    

    #[Route('/suppression/{id}', name: 'delete')]
    public function remove(Produits $produit): Response
    {
        $this->denyAccessUnlessGranted('PRODUCT_DELETE', $produit);
        return $this->render('admin/produit/suppression.html.twig', [
            'controller_name' => 'AdminProduitController',
        ]);
    }
}
