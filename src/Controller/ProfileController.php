<?php

namespace App\Controller;

use App\Entity\Commandes;
use App\Repository\CommandesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/profil', name: 'profile_')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CommandesRepository $commandeRepo): Response
    {
        $user = $this->getUser();

        if ($user) {
            $userId = $user->getId();
        }

        $commandes=$commandeRepo->findBy(['users'=>$userId]);

        return $this->render('profile/index.html.twig', [
            'controller_name' => 'Profile de l\'utilisateur',
            'commandes' => $commandes
        ]);
    }

    #[Route('/commandes', name: 'orders')]
    public function commandes(): Response
    {
        return $this->render('profile/commandes.html.twig', [
            'controller_name' => 'Commandes de l\'utilisateur',
        ]);
    }
}
