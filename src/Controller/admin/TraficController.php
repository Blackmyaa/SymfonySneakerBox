<?php

namespace App\Controller\admin;

use App\Form\Top5SalesType;
use App\Form\Flop5SalesType;
use App\Form\AnalyseVenteType;
use App\Form\AnalyseTraficType;
use App\Form\BestSalesDateType;
use App\Form\FlopSalesDateType;
use App\Repository\VisiteRepository;
use App\Repository\CommandesRepository;
use App\Repository\DetailCommandeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/frequentation', name: 'admin_frequentation_')]
class TraficController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, VisiteRepository $visiteRepository, DetailCommandeRepository $detailCommandeRepository, CommandesRepository $commandesRepo): Response
    {   
        //article le plus vendu 
        $form = $this->createForm(BestSalesDateType::class); 
        $form->handleRequest($request); 
        
        $mostSoldProduct = null; 
        
        if ($form->isSubmitted() && $form->isValid()) { 
            
            $data = $form->getData(); 
            $startDate = $data['start_date']; 
            $endDate = $data['end_date']; 
            
            $mostSoldProduct = $detailCommandeRepository->findMostSoldProduct($startDate, $endDate); 
        }

        // plus gros flop (article le moins vendu avec le plus gros stock)
        $formflop = $this->createForm(FlopSalesDateType::class); 
        $formflop->handleRequest($request);

        $leastSoldProduct = null;

        if ($formflop->isSubmitted() && $formflop->isValid()) { 
            
            $data = $formflop->getData(); 
            $startDate = $data['start_date']; 
            $endDate = $data['end_date'];
            
            $leastSoldProduct = $detailCommandeRepository->findLeastSoldProduct($startDate, $endDate); 
        }
        
        //5 articles les plus vendus general ou par catégorie
        $formTop5 = $this->createForm(Top5SalesType::class); 
        $formTop5->handleRequest($request); 
        
        $top5SoldProducts = []; 
        
        if ($formTop5->isSubmitted() && $formTop5->isValid()) { 
            $data = $formTop5->getData(); 
            
            $startDate = $data['start_date']; 
            $endDate = $data['end_date']; 
            
            if ($data['type'] === 'general') { 
                
                $top5SoldProducts = $detailCommandeRepository->findTop5SoldProducts($startDate, $endDate); 
            } else { 
                $top5SoldProducts = $detailCommandeRepository->findTop5SoldProducts($startDate, $endDate, $data['category']->getId());
            } 
        }

        // 5 articles les moins vendus general ou par catégorie
        $formFlop5 = $this->createForm(Flop5SalesType::class); 
        $formFlop5->handleRequest($request); 
        
        $flop5SoldProducts = []; 
        
        if ($formFlop5->isSubmitted() && $formFlop5->isValid()) { 
            $data = $formFlop5->getData(); 
            
            $startDate = $data['start_date']; 
            $endDate = $data['end_date']; 
            
            if ($data['type'] === 'general') { 
                
                $flop5SoldProducts = $detailCommandeRepository->findFlop5SoldProducts($startDate, $endDate); 
            } else { 
                $flop5SoldProducts = $detailCommandeRepository->findFlop5SoldProducts($startDate, $endDate, $data['category']->getId());
            } 
        }

        //Jour avec le plus grand nombre de ventes et plus gros montant de vente
        $formAnalysis = $this->createForm(AnalyseVenteType::class); 
        $formAnalysis->handleRequest($request);
        
        $salesAnalysis = null; 
        $noSalesMessage = null;
        
        if ($formAnalysis->isSubmitted() && $formAnalysis->isValid()) {
            $data = $formAnalysis->getData(); 
            $startDate = $data['start_date'];
            $endDate = $data['end_date']; 
            
            $salesAnalysis = $detailCommandeRepository->findSalesAnalysis($startDate, $endDate);
            
            if ($salesAnalysis) {
                $salesAnalysis['dayWithMostSales']['saleDay'] = (new \DateTimeImmutable($salesAnalysis['dayWithMostSales']['saleDay']))->format('d-m-y');
                $salesAnalysis['dayWithHighestSalesAmount']['saleDay'] = (new \DateTimeImmutable($salesAnalysis['dayWithHighestSalesAmount']['saleDay']))->format('d-m-y');
            }
            
            if ($salesAnalysis['dayWithMostSales'] === null && $salesAnalysis['dayWithHighestSalesAmount'] === null) { 
                $noSalesMessage = 'Aucune vente enregistrée pendant la période sélectionnée.'; 
            }
        }
        
        //Trouver le jour avec le plus de visites sur le site
        $formVisits = $this->createForm(AnalyseTraficType::class); 
        $formVisits->handleRequest($request); 
        
        $totalSalesAmount = null; 
        $visitDay =[];
        $statistics = null;

        if ($formVisits->isSubmitted() && $formVisits->isValid()) { 
            $data = $formVisits->getData(); 
            $startDate = $data['start_date'];
            $endDate = $data['end_date'];
            
            $statistics = $visiteRepository->findPeriodStatistics($startDate, $endDate);

            // Première étape : trouver les jours avec le plus de visites 
            $visitDay = $visiteRepository->findDaysWithMostVisits($startDate, $endDate);
            if ($visitDay) { 
                $visitDay[0]['visitDay'] = (new \DateTimeImmutable($visitDay[0]['visitDay']))->format('d-m-Y'); 
            }

            $totalSalesAmount = null; 

            if ($visitDay) { 
                $formattedDate = (new \DateTimeImmutable($visitDay[0]['visitDay']))->format('Y-m-d');

                $totalSalesAmount = $detailCommandeRepository->findTotalSalesAmountForDay(new \DateTimeImmutable($formattedDate)); 
            }

        }

        return $this->render('admin/frequentation/transformation/detailTrafic.html.twig', [
            'form' => $form->createView(), 
            'mostSoldProduct' => $mostSoldProduct,
            'formTop5' => $formTop5->createView(), 
            'top5SoldProducts' => $top5SoldProducts,
            'formFlop5' => $formFlop5->createView(),
            'flop5SoldProducts' => $flop5SoldProducts,
            'formflop'=>$formflop->createView(),
            'leastSoldProduct'=>$leastSoldProduct,

            'formAnalysis' => $formAnalysis->createView(), 
            'salesAnalysis' => $salesAnalysis, 
            'noSalesMessage' => $noSalesMessage,
            'formVisits'=>$formVisits,
            'visitDay'=>$visitDay,
            'totalSalesAmount'=>$totalSalesAmount,
            'statistics' => $statistics,
        ]);
    }
}
