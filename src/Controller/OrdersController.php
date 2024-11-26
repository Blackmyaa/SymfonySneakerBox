<?php

namespace App\Controller;

use App\Entity\Commandes;
use App\Entity\DetailCommande;
use App\Repository\ProduitsRepository;
use App\Repository\CommandesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/commandes', name: 'orders_')]
class OrdersController extends AbstractController
{
    #[Route('/ajout', name: 'add')]
    public function index(CommandesRepository $commandesRepository, SessionInterface $session, ProduitsRepository $produitsRepo, EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $panier = $session->get('panier', []);

        if ($panier === []){
            $this->addFlash('danger', 'Votre panier est vide');
            return $this->redirectToRoute('app_accueil');
        }

        //Le panier n'est pas vide, On crée la commande

        $order = new Commandes;

        //On remplit la commande
        $order->setUsers($this->getUser());
        $order->setReference(uniqid()); //A modifier pour y mettre AnnéeMoisJourHeureMinuteSecondes AAAAMMJJHHMnMnSS voir le DateType
        
        $today = (new \DateTimeImmutable('today'))->format('Y-m-d 00:00:00');
        $order->setRegisteredAt($today);

        //On parcourt le panier pour créer les détails de la commande
        foreach($panier as $item => $quantite){
            $orderDetails = new DetailCommande;

            //On va chercher le produit

            $produit=$produitsRepo->find($item);

            $prix = $produit->getPrix();

            //On crée le détail de la commande

            $orderDetails->setProduit($produit);
            $orderDetails->setPrix($prix);
            $orderDetails->setQuantite($quantite);
            
            //On ajoute les détails de la commande dans la commande
            $order->addDetailCommande($orderDetails);
        };

        //On persiste et on Flush
        $manager->persist($order);
        $manager->flush();

        $this->addFlash('success', 'Commande validée');

        $session->remove('panier');
        
        $commande = $commandesRepository->findOneBy([], ['created_at' => 'DESC']);
        $commandeId = $commande->getId();
    // integrer le code de la generation de pdf et mettre une redirection vers le compte client        
    
    return $this->redirectToRoute('generate_pdf', ['commandeId'=>$commandeId]);
    }
}

