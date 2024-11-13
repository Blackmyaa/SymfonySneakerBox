<?php

namespace App\Controller\admin;

use App\Entity\Commandes;
use App\Repository\ProduitsRepository;
use App\Repository\CommandesRepository;
use App\Repository\DetailCommandeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommandeController extends AbstractController
{
    #[Route('/admin/commandes', name: 'app_admin_commande')]
    public function listerCommande(CommandesRepository $commandesRepository, DetailCommandeRepository $detailCommandeRepository, ProduitsRepository $produitsRepository): Response
    {
        $commande = $commandesRepository->findAll();
        $commandeId = $commandesRepository->findById($commande);
        $detail = $detailCommandeRepository->findBy(['commande'=>$commandeId]);

        return $this->render('admin/commandes/listeCommandes.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/admin/commandes{reference}/details', name: 'app_admin_commande_details')]
    public function detailCommande(CommandesRepository $commandesRepo, DetailCommandeRepository $detailCommandeRepo, ProduitsRepository $produitsRepo, Commandes $commandes): Response
    {
        $ref = $commandesRepo->findByReference($commandes);

        $reference = $commandes->getReference();
        $users = $commandes->getUsers();

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

        return $this->render('admin/commandes/adminDetailCommandes.html.twig', [
            'commandeUtile' => $commandeUtile,
            'addition' => $addition,
            'commandes' => $commandes,
            'users'=> $users
        ]);
    }
}
