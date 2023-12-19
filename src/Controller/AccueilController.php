<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccueilController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function index(CategoriesRepository $categoriesRepo): Response
    {


        return $this->render('accueil/index.html.twig', [
            'nomSite' => 'MySneakerBox',
            'categories' => $categoriesRepo->findBy([],['categoryOrder' => 'asc']),
        ]);
    }
}
