<?php

namespace App\Controller;

use App\Service\SendMailService;
use App\Form\ResetPasswordFormType;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ResetPasswordRequestFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // à décommenter si on souhaite rediriger l'utilisateur vers son compte si il est déjà connecté
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('app_accueil');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername, 
            'error' => $error
        ]);
    }

    #[Route(path: '/deconnexion', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    // On va utiliser 2 routes pour réinitialiser le mdp en faisant 2 twig, le premier pour demander la reinitialisation le cond qui va faire la réinitialisation
    #[Route(path: '/oubli-MDP', name: 'forgotten_password')]
    public function mdpOublie(Request $request, UsersRepository $usersRepo, TokenGeneratorInterface $tokenGeneratorInterface, EntityManagerInterface $manager, SendMailService $mail):Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);

        //on traite le formulaire pour récuperer les datas

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // On va cherchet l'utilisateur par son email

            $user = $usersRepo->findOneByEmail($form->get('email')->getData());

            //On vérifie si on a un utilisateur
            if($user){
                //On genere un Token de reinitialisation a l'aide de TokenGeneratorInterface $tokenGeneratorInterface
                $token = $tokenGeneratorInterface->generateToken();
                $user->setResetToken($token);


                $manager->persist($user);
                $manager->flush($user);

                //On génére un lien de réinitialisation

                $url = $this->generateUrl('reset_pass', ['token'=>$token], UrlGeneratorInterface::ABSOLUTE_URL);

                //on crée les données du mail en utilisant le service mail

                $context = [
                'url' => $url,
                'user' => $user
                ];

                //on envoie le mail
                $mail->send(
                    'noreply@ecommerce.fr',
                    $user->getEmail(),
                    'réinitialisation de MDP',
                    'password_reset',
                    $context
                
                );

            $this->addFlash('success','Message envoyé avec succès');
            return $this->redirectToRoute('app_accueil');

            }

            //Si $user=null
            $this->addFlash('danger','Un probleme est survenu');
            return $this->redirectToRoute('app_accueil');
        }
        
        return $this->render('security/reset_password_request.html.twig', [
            'requestPassForm' => $form->createView()
        ]);
    }

    #[Route('/oubli-pass/{token}', name: 'reset_pass')]
    public function resetPass(string $token, Request $request, UsersRepository $usersRepo, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher) : Response
    {
        // verifions si on a  e token dans la bdd

        $user = $usersRepo->findOneByResetToken($token);
        
        
        if($user){

            $form = $this->createForm(ResetPasswordFormType::class);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                //On efface le Token
                $user->setResetToken('');
                $user->setPassword(
                    $passwordHasher->hashPassword($user,
                        $form->get('password')->getData()
                    )
                );
                $manager->persist($user);
                $manager->flush($user);

                $this->addFlash('success','Mot de passe modifié avec succès');
                return $this->redirectToRoute('app_login');



            }
            return $this->render('security/reset_password.html.twig', [
                'resetPassForm' => $form->createView()
            ]);
        }

        $this->addFlash('danger','Token invalide');
        return $this->redirectToRoute('app_accueil');
    }
}
