<?php

namespace App\Controller\admin;

use App\Repository\CategoriesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/categorie', name: 'admin_categorie_')]
class AdminCategorieController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoriesRepository $categorieRepo): Response
    {

        $categories = $categorieRepo->findAll([], ['categoryOrder' => 'asc']);
        return $this->render('admin/admin_categorie/index.html.twig', [
            'categories' => $categories,
        ]);
    }
}
