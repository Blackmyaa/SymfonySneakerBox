<?php

namespace App\Controller;

use App\Entity\Visite;
use DateTimeImmutable;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccueilController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function index(CategoriesRepository $categoriesRepo,Request $request, EntityManagerInterface $entityManager): Response
    {
        $visitorIp = $request->getClientIp();
        $today = (new \DateTimeImmutable('today'))->format('Y-m-d');

        $existingVisitor = $entityManager->getRepository(Visite::class)->findOneBy([
            'ipAddress' => $visitorIp,
            'visitedAt' => $today,
        ]);
        
        if (!$existingVisitor) {
            // Créer un nouvel objet Visitor
            $visitor = new Visite();
            $visitor->setIpAddress($visitorIp);
            $visitor->setVisitedAt($today);
        
            // Enregistrer dans la base de données
            $entityManager->persist($visitor);
            $entityManager->flush();
        }

        return $this->render('accueil/index.html.twig', [
            'nomSite' => 'MySneakerBox',
            'categories' => $categoriesRepo->findBy([],['categoryOrder' => 'asc']),
        ]);
    }
}
