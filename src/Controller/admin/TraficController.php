<?php

namespace App\Controller\admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/frequentation', name: 'admin_frequentation_')]
class TraficController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request): Response
    {
        return $this->render('admin/frequentation/transformation/detailTrafic.html.twig', [
        ]);
    }
}
