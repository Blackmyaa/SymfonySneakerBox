<?php

namespace App\Controller;

use App\Entity\Produits;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/produits', name: 'app_produits_')]
class ProduitsController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('produits/index.html.twig', [
            'controller_name' => 'ProduitsController',
        ]);
    }

    #[Route('/{slug}', name: 'details')]
    public function details(Produits $produits): Response
    {
        return $this->render('produits/details.html.twig', [
            'controller_name' => 'ProduitsController',
            'produits' => $produits // on peut remplacer par compact('produits')
        ]);
    }
}
