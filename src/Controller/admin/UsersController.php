<?php

namespace App\Controller\admin;

use App\Repository\UsersRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/utilisateurs', name: 'app_users_')]
class UsersController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(UsersRepository $userRepo): Response
    {
        $users = $userRepo->findAll();
        return $this->render('admin/users/index.html.twig', [
            'users' => $users,
        ]);
    }
}
