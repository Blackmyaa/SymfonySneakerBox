<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Commandes;
use App\Form\MdpEditFormType;
use App\Form\NomEditFormType;
use App\Form\MailEditFormType;
use App\Form\AdresseEditFormType;
use App\Repository\CommandesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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

    #[Route('/editUser/{id}', name: 'edit')]
    public function editUsers($id, Request $request, UserPasswordHasherInterface $userPasswordHasher, UserInterface $utilisateur, EntityManagerInterface $manager): Response
    {           
        $user = $manager->getRepository(Users::class)->find($id);
        
        $formMdp = $this->createForm(MdpEditFormType::class, $user);
        $formMdp->handleRequest($request);

        $formEmail = $this->createForm(MailEditFormType::class , $user);
        $formEmail ->handleRequest($request);

        $formNom = $this->createForm(NomEditFormType::class , $user);
        $formNom ->handleRequest($request);

        //Pour l'adresse il faut creer une fonction qui gerera l'autocompletion et appeler cette fonction apres l'injection du formulaire dans le DOM (voir fichier edtProfil.html.twig) 
        $formAdresse = $this-> createForm(AdresseEditFormType::class, $user);
        $formAdresse ->handleRequest($request);

        if ($formMdp->isSubmitted() && $formMdp->isValid()) {
            
            $user->setPassword(
                $userPasswordHasher->hashPassword($user,
                    $formMdp->get('password')->getData()
                )
            );
            
            $manager->persist($user);
            $manager->flush($user);
            $this->addFlash('success', 'Modification effectuée avec succès');


            return $this->redirectToRoute('profile_index', ['id' => $user->getId()]);
        }

        if ($formEmail->isSubmitted() && $formEmail->isValid()) {
            $manager->persist($user);

            $manager->flush();
            $this->addFlash('success', 'Modification effectuée avec succès');

            return $this->redirectToRoute('profile_index', ['id' => $user->getId()]);
        }

        if ($formNom->isSubmitted() && $formNom->isValid()) {
            $manager->persist($user);

            $manager->flush();
            $this->addFlash('success', 'Modification effectuée avec succès');

            return $this->redirectToRoute('profile_index', ['id' => $user->getId()]);
        }

        if ($formAdresse->isSubmitted() && $formAdresse->isValid()) {
            $manager->persist($user);

            $manager->flush();
            $this->addFlash('success', 'Modification effectuée avec succès');

            return $this->redirectToRoute('profile_index', ['id' => $user->getId()]);
        }



        return $this->render('profile/modifier.html.twig', [
            'formMdp' => $formMdp->createView(),
            'formEmail'=>$formEmail->createView(),
            'formNom' =>$formNom->createView(),
            'formAdresse'=>$formAdresse->createView(),
            'user' => $user,
        ]);
    }
        
    #[Route('/editUser/{id}/editNom', name: 'edit_Nom')]
    public function editNom($id, Request $request, UserInterface $utilisateur, EntityManagerInterface $manager)
    {
        if(!$utilisateur){
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('app_login');
        }
        $user = $manager->getRepository(Users::class)->find($id);
        $formNom = $this->createForm(NomEditFormType::class , $user);

        $this->addFlash('success', 'Modification effectuée avec succès');
        return $this->render('profile/edit/nom.html.twig', [
            'formNom' => $formNom->createView(),
        ]);
    }

    #[Route('/editUser/{id}/editMail', name: 'edit_Mail')]
    public function editMail($id, Request $request, UserInterface $utilisateur, EntityManagerInterface $manager): Response
    {  
        if(!$utilisateur){
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('app_login');
        }
        $user = $manager->getRepository(Users::class)->find($id);
        $formEmail = $this->createForm(MailEditFormType::class , $user);

        $this->addFlash('success', 'Modification effectuée avec succès');
        return $this->render('profile/edit/email.html.twig', [
            'formEmail'=>$formEmail
        ]);
    } 

    #[Route('/editUser/{id}/editMDP', name: 'edit_MDP')]
    public function editMDP($id, Request $request, UserInterface $utilisateur, EntityManagerInterface $manager)
    {   
        if(!$utilisateur){
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('app_login');
        }
        $user = $manager->getRepository(Users::class)->find($id);
        $formMdp = $this->createForm(MdpEditFormType::class , $user);
        
        $this->addFlash('success', 'Modification effectuée avec succès');
        return $this->render('profile/edit/mdp.html.twig', [
            'formMdp'=>$formMdp,
        ]);
    }

    #[Route('/editUser/{id}/editAdresse', name: 'edit_Adresse')]
    public function editAdresse($id, Request $request, UserInterface $utilisateur, EntityManagerInterface $manager)
    {
        if(!$utilisateur){
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('app_login');
        }
        $user = $manager->getRepository(Users::class)->find($id);
        $formAdresse = $this->createForm(AdresseEditFormType::class , $user);


        return $this->render('profile/edit/adresse.html.twig', [
            'formAdresse'=>$formAdresse,
        ]);
    }
    

}
