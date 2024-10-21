<?php

namespace App\Controller\admin;

use App\Entity\Categories;
use App\Repository\UsersRepository;
use App\Repository\ProduitsRepository;
use App\Repository\CategoriesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin', name: 'admin_')]
class AdminDashboardController extends AbstractController
{
    #[Route('/MonDashboard', name: 'index')]
    public function index(CategoriesRepository $categoriesRepository,UsersRepository $usersRepository, ProduitsRepository $produitsRepository, Request $request): Response
    {
        $prod = $produitsRepository->findAll();

        // Remplissage de la partie gestion de stock du dashboard
        $nombreRef = 0;
        $stockTotal = 0;
        $prixTotal = 0;
        $pieces = 0;
        $prixMoyen = 0;

        foreach($prod as $produit)
        {
            //Calcul du nombre de référence
            $nombreRef = $nombreRef +1;
            //calcul du stock global de la catégorie
            $stockTotal = $stockTotal + $produit->getStock();
            
            //calcul de la valeur
            $prixTotal = ($prixTotal + ($produit->getStock() * $produit->getPrix()));
        }
        $prixTotal = $prixTotal/100;

        if ( $stockTotal != 0) {
            $prixMoyen = $prixTotal / $stockTotal;
        }
        else {
            $prixMoyen = 0;
        }
        
        if ($stockTotal > 1) {
            $pieces = 'pièces';
        }
        else {
            $pieces = 'pièce';
        }

        //remplissage de la partie dernier Event

        $lastProduit = $produitsRepository->findOneBy([],['created_at'=>'DESC']);
        $lastUser = $usersRepository->findOneBy([],['cree_le'=>'DESC']);


        return $this->render('admin/admin_dashboard/index.html.twig', [
            'nombreRef'=> $nombreRef,
            'stockTotal'=>$stockTotal,
            'pieces'=>$pieces,
            'prixTotal'=> $prixTotal,
            'prixMoyen' => $prixMoyen,
            'lastProduit'=>$lastProduit,
            'lastUser'=>$lastUser

        ]);
    }
}
