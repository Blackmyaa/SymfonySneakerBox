<?php

namespace App\Controller;

use App\Entity\Produits;
use App\Repository\ProduitsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/cart', name: 'cart_')]
class CartController extends AbstractController
{   #[Route('/', name: 'index')]
public function index(ProduitsRepository $produitRepo, SessionInterface $session): Response
{
    //On récupére le panier existant
    $panier = $session->get('panier', []);

    // On initialise des variables 
    $data = [];
    $total = 0;

    foreach($panier as $id => $quantite){
        $produit = $produitRepo-> find($id);
    
        $data[] = [
            'produit' => $produit,
            'quantite' => $quantite,
        ];
        $total += $produit->getPrix()*$quantite;
    
    }

    return $this->render('cart/index.html.twig', [
        'data' => $data,
        'total' => $total,
    ]);
}
    #[Route('/add/{id}', name: 'add')]
    public function add(Produits $produits, SessionInterface $session): Response
    {
        //On récupére l'id du produit
        $id = $produits->getId();

        //On récupére le panier existant
        $panier = $session->get('panier', []);

        //On ajoute le produit dans le panier si il n'y est pas sinon on incrémente sa quantité
        if (empty($panier[$id])) {
            $panier[$id] = 1;
        
        } else {
            $panier[$id]++;
        }

        $panier = $session->set('panier', $panier);

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/remove/{id}', name: 'remove')]
    public function remove(Produits $produits, SessionInterface $session): Response
    {
        //On récupére l'id du produit
        $id = $produits->getId();

        //On récupére le panier existant
        $panier = $session->get('panier', []);

        //On retire le produit dans le panier si il n'y est pas sinon on décrémente sa quantité
        if (!empty($panier[$id])) {
            if ($panier[$id] > 1 ) {
                $panier[$id]--;
            }else{
                unset($panier[$id]);
            }
        }

        $panier = $session->set('panier', $panier);

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Produits $produits, SessionInterface $session): Response
    {
        //On récupére l'id du produit
        $id = $produits->getId();

        //On récupére le panier existant
        $panier = $session->get('panier', []);

        //On retire le produit dans le panier
        if (!empty($panier[$id])) {
                unset($panier[$id]);
        }

        $panier = $session->set('panier', $panier);

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/empty', name: 'empty')]
    public function empty(SessionInterface $session)
    {
        $panier = $session->get('panier', []);
        
        if ($panier === []){
            $this->addFlash('danger', 'Votre panier est vide');
            return $this->redirectToRoute('app_accueil');
        }
        $session->remove('panier');
        
        return $this->redirectToRoute('cart_index');
    }
}
