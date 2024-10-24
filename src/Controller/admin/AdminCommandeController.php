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
            //'addition' => $addition
        ]);
    }
}
