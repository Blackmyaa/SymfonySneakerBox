<?php

namespace App\Controller;

use App\Entity\Categories;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/categories', name: 'categories_')]
class CategoriesController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('categories/index.html.twig', [
            'controller_name' => 'CategoriesController',
        ]);
    }

    #[Route('/{slug}', name: 'list')]
    public function list(Categories $categories): Response
    {
        // On va chercher tout les produits de la catégorie

        $produits = $categories->getProduits();

        return $this->render('categories/liste.html.twig', [
            'controller_name' => 'CategoriesController',
            'categories' => $categories,
            'produits'=> $produits
        ]);
    }
}
