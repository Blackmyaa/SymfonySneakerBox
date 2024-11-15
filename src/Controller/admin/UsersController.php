<?php

namespace App\Controller\admin;

use App\Entity\Users;
use App\Service\JWTService;
use App\Form\AddAdminFormType;
use App\Form\AdminEditFormType;
use App\Service\SendMailService;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin/utilisateurs', name: 'app_users_')]
class UsersController extends AbstractController
{
    private $emailService;

    public function __construct(SendMailService $emailService)
    {
        $this->emailService = $emailService;
    }

    #[Route('/', name: 'index')]
    public function index(UsersRepository $userRepo): Response
    {
        $users = $userRepo->findAll();
        return $this->render('admin/users/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/infos/{id}', name: 'infos')]
    public function infos($id, UsersRepository $userRepo): Response
    {
        $user = $userRepo->findOneById($id);
        return $this->render('admin/users/details.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/adminListe', name: 'admin_liste')]
    public function findAdmin(UsersRepository $userRepo): Response
    {
        $admins = $userRepo->findAdminsAndProductAdmins();
        return $this->render('admin/users/adminListe.html.twig', [
            'admins' => $admins,
        ]);
    }

    #[Route('/add-admin', name: 'admin_add')]
    public function addAdmin(UsersRepository $userRepo, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $manager, SendMailService $emailService, JWTService $jwt): Response
    {
        $currentUser = $this->getUser();

        if (!$currentUser->hasRole('ROLE_ADMIN') && !$currentUser->hasRole('ROLE_PRODUCT_ADMIN')) { 
            throw $this->createAccessDeniedException('Vous n\'avez pas les droits pour créer un administrateur.'); 
        }
        
        $admin = new Users;
        $formAdmin = $this->createForm(AddAdminFormType::class, $admin, ['currentUser' => $currentUser]);
        $formAdmin->handleRequest($request);

        if ($formAdmin->isSubmitted() && $formAdmin->isValid()) {
            $admin->setPassword(
                $userPasswordHasher->hashPassword($admin,
                    $formAdmin->get('password')->getData()
                )
            );

            //On prépare le context de l'email pour prévenir l'utilisateur qu'on vien de créer qu'il est admin

            $recipient = $admin->getEmail();

            // Préparer le contexte pour l'email
            $context = [
                'nom' => $admin->getNom(),
                'prenom' => $admin->getPrenom(),
                'user_email' => $admin->getEmail(),
                'password' => $formAdmin->get('password')->getData(),
                'roles'=> $admin->getRoles()
            ];
            $this->emailService->send(
                'no-reply@mysneakerbox.fr',
                $recipient,
                'Vous etes notre nouvel Administrateur',
                'adminRegister', // Assurez-vous d'avoir un template email/contact.html.twig
                $context);
                
                $manager->persist($admin);
                $manager->flush($admin);

            $header = [
                'typ' =>'JWT',
                'alg' => 'HS256'
                ];
    
                //On crée le payload
                $payload = [
                'user_id' => $admin->getId()
                ];
    
                //On génére le Token
                $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));
                
                //On envoie le mail de confirmation
                $this->emailService->send(
                    'no-reply@mysneakerbox.fr',
                    $admin->getEmail(),
                    'Votre compte Administrateur MySneakersBox a été créé',
                    'register',
                    [
                    'user'=>$admin,
                    'token'=>$token
                    ]
                );

            $this->addFlash('success', 'Administrateur ajouté avec succès');

            return $this->redirectToRoute('app_users_admin_liste');

        }
        return $this->render('admin/users/addAdmin.html.twig', [
            'formAdmin'=>$formAdmin->createView()
        ]);
    }

    #[Route('/edit-admin/{id}', name: 'admin_edit')]
    public function editAdmin($id,UsersRepository $userRepo, Request $request, EntityManagerInterface $manager): Response
    {
        $user = $userRepo->findOneById($id);
        $currentUser = $this->getUser();

        if (!$currentUser->hasRole('ROLE_ADMIN') && !$currentUser->hasRole('ROLE_PRODUCT_ADMIN')) { 
            throw $this->createAccessDeniedException('Vous n\'avez pas les droits pour créer un administrateur.'); 
        }
        
        $formAdmin = $this->createForm(AdminEditFormType::class, $user, ['currentUser' => $currentUser]);
        $formAdmin->handleRequest($request);

        if ($formAdmin->isSubmitted() && $formAdmin->isValid()){
            $manager->persist($user);
            $manager->flush($user);

            $this->addFlash('success', 'Modif effectuée avec succès');

            return $this->redirectToRoute('app_users_admin_liste');

        }

        return $this->render('admin/users/editAdmin.html.twig', [
            'formAdmin'=>$formAdmin->createView(),
            'user'=>$user
        ]);
    }
}