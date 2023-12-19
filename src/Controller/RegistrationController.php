<?php

namespace App\Controller;

use App\Entity\Users;
use App\Service\JWTService;
use App\Service\SendMailService;
use App\Form\RegistrationFormType;
use App\Repository\UsersRepository;
use App\Security\UserAuthAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthAuthenticator $authenticator, EntityManagerInterface $entityManager, SendMailService $mail, JWTService $jwt): Response
    {
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email
            
            //On génére le JWT de l'utilisateur
            //On crée le Header
            $header = [
            'typ' =>'JWT',
            'alg' => 'HS256'
            ];

            //On crée le payload
            $payload = [
            'user_id' => $user->getId()
            ];

            //On génére le Token

            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            //On envoie le mail de confirmation
            $mail->send(
            'no-reply@mysneakerbox.fr',
            $user->getEmail(),
            'Votre compte MySneakersBox a été créé',
            'register',
            [
            'user'=>$user,
            'token'=>$token
            ]);

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[ROUTE('/verif/{token}', name: 'verif_user')]
    public function verif_user($token, JWTService $jwt, UsersRepository $userRepository, EntityManagerInterface $manager): Response
    {
        //On verifie si le token est valide, n'a pas expiré et n'a pas été modifié
        if($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))){

            //On récupére le payload
            $payload = $jwt->getPayload($token);

            // On récupére le user du token
            $user = $userRepository->find($payload['user_id']);

            //On vérifie que l'utilisateur existe et qu'il n'a pas encore vérifié son comte
            if($user && !$user->getIsVerified()){
                $user->setIsVerified(true);
                $manager->flush($user);
                $this->addFlash('success', 'Votre compte a été activé avec succès');

                return $this->redirectToRoute('profile_index');
            }
        }
        
        //Si une des conditions n'est pas respectée
        $this->addFlash('danger', 'le token est invalide ou a expiré');

        return $this->redirectToRoute('app_login');
    }

    #[Route('/renvoiverif', name: 'resend_verif')]
    public function resendVerif(JWTService $jwt, SendMailService $mail, UsersRepository $usersRepository): Response
    {
        $user = $this->getUser();

        if(!$user){
            $this->addFlash('danger', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_login');    
        }

        if($user->getIsVerified()){
            $this->addFlash('warning', 'Cet utilisateur est déjà activé');
            return $this->redirectToRoute('profile_index');    
        }

        // On génère le JWT de l'utilisateur
        // On crée le Header
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        // On crée le Payload
        $payload = [
            'user_id' => $user->getId()
        ];

        // On génère le token
        $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

        // On envoie un mail
        $mail->send(
            'no-reply@mysneakerbox.fr',
            $user->getEmail(),
            'Votre compte MySneakersBox a été créé',
            'register',
            [
            'user'=>$user,
            'token'=>$token
            ]);

        $this->addFlash('success', 'Email de vérification envoyé');
        return $this->redirectToRoute('profile_index');
    }
}
