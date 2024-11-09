<?php

namespace App\Controller\admin;

use App\Entity\Images;
use App\Entity\Produits;
use App\Form\ProduitsFormType;
use App\Service\PictureService;
use App\Form\ProduitsEditFormType;
use App\Repository\ProduitsRepository;
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
    public function index(ProduitsRepository $produitRepository, Request $request): Response
    {
        $produit =  $produitRepository->findAll();
        
        return $this->render('admin/produit/index.html.twig', [
            'produit' => $produit,
        ]);
    }
    
    #[Route('/ajout', name: 'add')]
    public function add(Request $request, EntityManagerInterface $manager, SluggerInterface $slugger, PictureService $pictureService): Response
    {
        //On crée un "nouveau produit"
        $produit = new Produits();
        
        //On crée le formulaire
        $formProduit = $this->createForm(ProduitsFormType::class, $produit);
        
        //On traite la requete
        $formProduit->handleRequest($request);
        
        //On vérifie si le formulaire est soumis ET Valide
        if($formProduit->isSubmitted() && $formProduit->isValid()){
            //On récupére les images
            $images = $formProduit->get('images')->getData();
            
            foreach($images as $image){
                // On définit le dossier de destination
                $folder = 'produits';

                // On appelle le service d'ajout
                $fichier = $pictureService->add($image, $folder, 300, 300);

                $img = new Images();
                $img->setNom($fichier);
                $produit->addImage($img); // on utilise la fonction addImage qui se situe dans l'entité Produit
            }
            
            //On genere le slug
            $nomProduit = $produit->getNom();
            // Transforme le texte en minuscules avant de créer le slug
            $nomProduit = mb_strtolower($nomProduit, 'UTF-8');

            $slug = $slugger->slug($nomProduit);
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
            'formProduit'=>$formProduit->createView(),
        ]);
    }
    
    #[Route('/edition/{id}', name: 'edit')]
    public function edit(Produits $produit, Request $request, EntityManagerInterface $manager, SluggerInterface $slugger, PictureService $pictureService): Response
    {
        //On vérifie si l'utilisateur peut editer avec le voteur
        $this->denyAccessUnlessGranted('PRODUCT_EDIT', $produit);
        
        //On divise le prix par 100
        $prix = $produit->getPrix() / 100;
        $produit->setPrix($prix);
        
        
        //On crée le formulaire
        $formProduit = $this->createForm(ProduitsEditFormType::class, $produit);
        
        //On traite la requete
        $formProduit->handleRequest($request);
        
        //On vérifie si le formulaire est soumis ET Valide
        if($formProduit->isSubmitted() && $formProduit->isValid()){

            $images = $formProduit->get('images')->getData();
            
            foreach($images as $image){
                // On définit le dossier de destination
                $folder = 'produits';

                // On appelle le service d'ajout
                $fichier = $pictureService->add($image, $folder, 300, 300);

                $img = new Images();
                $img->setNom($fichier);
                $produit->addImage($img); // on utilise la fonction addImage qui se situe dans l'entité Produit
            }
            //On genere e slug
            $slug = $slugger->slug($produit->getNom())->lower();
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

    #[Route('/suppression/image/{id}', name: 'delete_image', methods: ['DELETE'])]
    public function deleteImage(Images $image, Request $request, EntityManagerInterface $manager, PictureService $pictureService): Response
    {
       // On récupère le contenu de la requête
        $data = json_decode($request->getContent(), true);

        if($this->isCsrfTokenValid('delete' . $image->getId(), $data['_token'])){
           // Le token csrf est valide
           // On récupère le nom de l'image
            $nom = $image->getNom();

            if($pictureService->delete($nom, 'produits', 300, 300)){
               // On supprime l'image de la base de données
                $manager->remove($image);
                $manager->flush();

                return new JsonResponse(['success' => true], 200);
            }
           // La suppression a échoué
            return new JsonResponse(['error' => 'Erreur de suppression'], 400);
        }

        return new JsonResponse(['error' => 'Token invalide'], 400);
        
    }

    #[Route('/{slug}', name: 'details')]
    public function details(Produits $produits): Response
    {
        return $this->render('admin/produit/detailsProduitAdmin.html.twig', [
            'produits' => $produits // on peut remplacer par compact('produits')
        ]);
    }
}
