<?php

namespace App\Controller\admin;

use App\Entity\Categories;
use App\Repository\UsersRepository;
use App\Repository\VisiteRepository;
use App\Repository\ProduitsRepository;
use App\Repository\CommandesRepository;
use App\Repository\CategoriesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin', name: 'admin_')]
class AdminDashboardController extends AbstractController
{
    #[Route('/MonDashboard', name: 'index')]
    public function index(CategoriesRepository $categoriesRepository,UsersRepository $usersRepository, ProduitsRepository $produitsRepository, Request $request, VisiteRepository $visiteRepository, CommandesRepository $commandesRepository): Response
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

        $lastOrder = $commandesRepository->findLastOrder();
        $lastReferenceOrder = $lastOrder->getReference();
        
        //$lastMontantVente = $lastOrder ? $commandesRepository->findLastOrderAmount($lastOrder) : 0;

        if ($lastOrder) { 
            $lastMontantVente = $commandesRepository->findLastOrderAmount($lastOrder); 
            $userName = $lastOrder->getUsers()->getNom(); 
            $userLastName = $lastOrder->getUsers()->getPrenom();
            
        }
        else {
            $lastMontantVente = 0;
            $userName = ''; 
            $userLastName = '';
        }

        //Remplissage de la partie compteur de visites
        
        $visitsToday = $visiteRepository->findTodaysVisits();
        $visitsYesterday = $visiteRepository->findYesterdaysVisits();

        $visitsThisWeek = $visiteRepository->findCurrentWeekVisitsCount();
        $visitsLastWeek = $visiteRepository->findLastWeekVisitsCount();

        $visitsThisMonth = $visiteRepository->findCurrentMonthVisitsCount();
        $visitsLastMonth = $visiteRepository->findLastMonthVisitsCount();

        $visitsThisYear = $visiteRepository->findCurrentYearVisitsCount();
        $visitsLastYear = $visiteRepository->findLastYearVisitsCount();

        //Remplissage de la partie Vente

        //Pour le total on précise le cas ou si le nombre de commandes correspondant est égal à 0 on return 0 sinon erreur car le total sera égal NULL
        $numberOfSalesThisYear = $commandesRepository->countCurrentYearSales(); 
        $totalMontantThisYear = $numberOfSalesThisYear > 0 ? $commandesRepository->sumCurrentYearSales() / 100 : 0;

        $numberOfSalesLastYear = $commandesRepository->countLastYearSales(); 
        $totalMontantLastYear = $numberOfSalesLastYear > 0 ? $commandesRepository->sumLastYearSales() / 100 : 0;
        
        $numberOfSalesLastMonth = $commandesRepository->countLastMonthSales(); 
        $totalMontantLastMonth = $numberOfSalesLastMonth > 0 ? $commandesRepository->sumLastMonthSales() / 100 : 0;

        $numberOfSalesThisMonth = $commandesRepository->countThisMonthSales();
        $totalMontantThisMonth = $numberOfSalesThisMonth > 0 ? $commandesRepository->sumThisMonthSales() / 100 : 0;

        $numberOfSalesLastWeek = $commandesRepository->countLastWeekSales();
        $totalMontantLastWeek = $numberOfSalesLastWeek > 0 ? $commandesRepository->sumLastWeekSales() / 100 : 0;

        $numberOfSalesThisWeek = $commandesRepository->countThisWeekSales();
        $totalMontantThisWeek = $numberOfSalesThisWeek > 0 ? $commandesRepository->sumThisWeekSales() / 100 : 0;

        $numberOfSalesYesterday = $commandesRepository->countYesterdaysSales(); 
        $totalMontantYesterday = $numberOfSalesYesterday > 0 ? $commandesRepository->sumYesterdaysSales() / 100 : 0;
        
        $numberOfSalesToday = $commandesRepository->countTodaysSales(); 
        $totalMontantToday = $numberOfSalesToday > 0 ? $commandesRepository->sumTodaysSales() / 100 : 0;



        return $this->render('admin/admin_dashboard/index.html.twig', [
            'nombreRef'=> $nombreRef,
            'stockTotal'=>$stockTotal,
            'pieces'=>$pieces,
            'prixTotal'=> $prixTotal,
            'prixMoyen' => $prixMoyen,
            'lastProduit'=>$lastProduit,
            'lastUser'=>$lastUser,
            'lastReferenceVente'=>$lastReferenceOrder,
            'lastVente'=>$lastMontantVente,
            'userName'=>$userName,
            'userLastName'=>$userLastName,

            'visitsToday'=>$visitsToday,
            'visitsYesterday'=>$visitsYesterday,

            'visitsThisWeek'=>$visitsThisWeek,
            'visitsLastWeek'=>$visitsLastWeek,

            'visitsThisMonth'=>$visitsThisMonth,
            'visitsLastMonth'=>$visitsLastMonth,

            'visitsThisYear'=>$visitsThisYear,
            'visitsLastYear'=>$visitsLastYear,

            'numberOfSalesLastMonth'=> $numberOfSalesLastMonth,
            'totalMontantLastMonth'=> $totalMontantLastMonth,

            'numberOfSalesThisMonth'=>$numberOfSalesThisMonth,
            'totalMontantThisMonth'=>$totalMontantThisMonth,

            'numberOfSalesLastWeek'=>$numberOfSalesLastWeek,
            'totalMontantLastWeek'=>$totalMontantLastWeek,

            'numberOfSalesThisWeek'=>$numberOfSalesThisWeek,
            'totalMontantThisWeek'=>$totalMontantThisWeek,

            'numberOfSalesYesterday'=>$numberOfSalesYesterday,
            'totalMontantYesterday'=>$totalMontantYesterday,

            'numberOfSalesToday'=>$numberOfSalesToday,
            'totalMontantToday'=>$totalMontantToday,

            'numberOfSalesThisYear'=>$numberOfSalesThisYear,
            'totalMontantThisYear'=>$totalMontantThisYear,

            'numberOfSalesLastYear'=>$numberOfSalesLastYear,
            'totalMontantLastYear'=>$totalMontantLastYear

        ]);
    }
}
