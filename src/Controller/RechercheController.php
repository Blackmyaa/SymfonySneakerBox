<?php

namespace App\Controller;

use App\Form\RechercheFormType;
use App\Repository\ProduitsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RechercheController extends AbstractController
{
    #[Route('/recherche', name: 'search_product')]
    public function recherche(Request $request, ProduitsRepository $produitsRepository): Response
    {   
        $query = $request->query->get('query');
        $produits = [];

        if ($query) {
            $produits = $produitsRepository->findByQuery($query);
            return $this->render('recherche/resultatsRecherche.html.twig', [
                'produits' => $produits,
                'query' => $query,
            ]);
        }
        else {
            return $this->redirectToRoute('search_form');        
        }

        return $this->render('recherche/resultatsRecherche.html.twig', [
            'controller_name' => 'RechercheController',
        ]);
    }

    #[Route('/recherche-form', name: 'search_form')]
    public function rechercheForm(Request $request, ProduitsRepository $produitsRepository): Response
    {   
        $form_recherche = $this->createForm(RechercheFormType::class);
        $form_recherche->handleRequest($request);
        $produits = [];

        if ($form_recherche->isSubmitted() && $form_recherche->isValid()) {
            $query = $form_recherche->get('query')->getData();
            $description = $form_recherche->get('description')->getData();
            $minPrice = $form_recherche->get('minPrice')->getData();
            $maxPrice = $form_recherche->get('maxPrice')->getData();
            $produits = $produitsRepository->findByFilters($query, $description, $minPrice, $maxPrice);
        }

        return $this->render('recherche/formRecherche.html.twig', [
            'searchForm' => $form_recherche->createView(),
            'produits' => $produits,

        ]);
    }
}