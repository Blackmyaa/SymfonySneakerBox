<?php

namespace App\Controller\admin;

use App\Entity\Categories;
use App\Repository\UsersRepository;
use App\Repository\ProduitsRepository;
use App\Repository\CategoriesRepository;
use App\Repository\VisitCounterRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin', name: 'admin_')]
class AdminDashboardController extends AbstractController
{
    #[Route('/MonDashboard', name: 'index')]
    public function index(VisitCounterRepository $visitRepository, CategoriesRepository $categoriesRepository,UsersRepository $usersRepository, ProduitsRepository $produitsRepository, Request $request): Response
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

        //Remplissage de la partie compteur de visites
        $now = new \DateTime(); 
        $startOfDay = (clone $now)->setTime(0, 0); 
        $yesterday = (clone $startOfDay)->modify('-1 day'); 
        $startOfWeek = (clone $startOfDay)->modify('this week'); 
        $startOfLastWeek = (clone $startOfWeek)->modify('-1 week'); 
        $startOfMonth = (clone $startOfDay)->modify('first day of this month'); 
        $startOfLastMonth = (clone $startOfMonth)->modify('-1 month'); 
        $startOfYear = (clone $startOfDay)->modify('first day of January this year'); 
        $startOfLastYear = (clone $startOfYear)->modify('-1 year'); 
        
        $visitsToday = $visitRepository->countVisits($startOfDay, $now); 
        $visitsYesterday = $visitRepository->countVisits($yesterday, $startOfDay); 
        $visitsThisWeek = $visitRepository->countVisits($startOfWeek, $now); 
        $visitsLastWeek = $visitRepository->countVisits($startOfLastWeek, $startOfWeek); 
        $visitsThisMonth = $visitRepository->countVisits($startOfMonth, $now); 
        $visitsLastMonth = $visitRepository->countVisits($startOfLastMonth, $startOfMonth); 
        $visitsThisYear = $visitRepository->countVisits($startOfYear, $now); $visitsLastYear = 
        $visitRepository->countVisits($startOfLastYear, $startOfYear); 
        $nonConnectedVisitsToday = $visitRepository->countVisits($startOfDay, $now, false); 
        $connectedVisitsToday = $visitRepository->countVisits($startOfDay, $now, true);



        return $this->render('admin/admin_dashboard/index.html.twig', [
            'nombreRef'=> $nombreRef,
            'stockTotal'=>$stockTotal,
            'pieces'=>$pieces,
            'prixTotal'=> $prixTotal,
            'prixMoyen' => $prixMoyen,
            'lastProduit'=>$lastProduit,
            'lastUser'=>$lastUser,

            'visitsToday' => $visitsToday, 
            'visitsYesterday' => $visitsYesterday, 
            'visitsThisWeek' => $visitsThisWeek, 
            'visitsLastWeek' => $visitsLastWeek, 
            'visitsThisMonth' => $visitsThisMonth, 
            'visitsLastMonth' => $visitsLastMonth, 
            'visitsThisYear' => $visitsThisYear, 
            'visitsLastYear' => $visitsLastYear, 
            'nonConnectedVisitsToday' => $nonConnectedVisitsToday, 
            'connectedVisitsToday' => $connectedVisitsToday,

        ]);
    }
}
