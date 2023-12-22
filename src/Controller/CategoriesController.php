<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Repository\ProduitsRepository;
use Symfony\Component\HttpFoundation\Request;
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
    public function list(Categories $categories, ProduitsRepository $produitsRepository, Request $request): Response
    {
        // On va chercher tout les produits de la catégorie

        //On utilise la fonction créée dans le repository pour afficher la quete en fixant les parametres
        $page = $request->query->getInt('page', 1);

        //Pour changer le numero de page dans l'affichage des résultat on va chercher le numérode page dans l'url
        $produits = $produitsRepository->findProductPaginated($page, $categories->getSlug(), 6);
        //findProductPaginated(1, $categories->getSlug(), 2) (numero de page, affichage des produits, nombre de produits par page)

        return $this->render('categories/liste.html.twig', [
            'controller_name' => 'CategoriesController',
            'categories' => $categories,
            'produits'=> $produits
        ]);
    }
}
