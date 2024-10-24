<?php

namespace App\Controller;

use App\Entity\Commandes;
use App\Repository\ProduitsRepository;
use App\Repository\CommandesRepository;
use App\Repository\DetailCommandeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderDetailsController extends AbstractController
{
    #[Route('/order{reference}/details', name: 'app_order_details')]
    public function index(CommandesRepository $commandesRepo, DetailCommandeRepository $detailCommandeRepo, ProduitsRepository $produitsRepo, Commandes $commandes): Response
    {   
        $ref = $commandesRepo->findByReference($commandes);

        $reference = $commandes->getReference();

        $commande = $commandesRepo->findBy(['reference' => $reference]);
        $commandeId = $commandesRepo->findById($commande);

        $commandeUtile = $detailCommandeRepo->findBy(['commande'=>$commandeId]);

        $addition = 0;
        foreach ($commandeUtile as $produit) {
            
            $prix = $produit->getPrix();
            $quantite = $produit->getQuantite();
            
            $addition = ($quantite * $prix) + $addition;
        }
        $addition = $addition / 100;


        // dd($commandeUtile);

        return $this->render('order_details/facture.html.twig', [
            'controller_name' => 'OrderDetailsController',
            'commandeUtile' => $commandeUtile,
            'addition' => $addition,
            'commandes' => $commandes
        ]);
    }
}
