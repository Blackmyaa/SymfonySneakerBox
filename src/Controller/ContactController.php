<?php

// src/Controller/ContactController.php
namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Users;
use App\Form\ContactFormType;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

class ContactController extends AbstractController
{
    private $roleHierarchy;
    private $userRepository;
    private $emailService;

    public function __construct(RoleHierarchyInterface $roleHierarchy, EntityManagerInterface $entityManager, SendMailService $emailService)
    {
        $this->roleHierarchy = $roleHierarchy;
        $this->userRepository = $entityManager->getRepository(Users::class);
        $this->emailService = $emailService;
    }

    private function getAdminEmails()
    {
        // Utiliser le repository injecté pour obtenir tous les utilisateurs
        $users = $this->userRepository->findAll();
        $adminEmails = [];
        foreach ($users as $user) {
            // Utiliser isGranted pour vérifier si l'utilisateur a le rôle ROLE_ADMIN
            // if ($this->isGranted('["ROLE_ADMIN"]', $user)) {
            //     $adminEmails[] = $user->getEmail();
            // }

            if (in_array("ROLE_ADMIN", $user->getRoles()) || in_array('ROLE_PRODUCT_ADMIN', $user->getRoles())) { 
                $adminEmails[] = $user->getEmail(); 
            }
        }
        
        return $adminEmails;
    }

    #[Route("/contact", name:"contact")]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactFormType::class, $contact);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrer le message de contact dans la base de données
            $entityManager->persist($contact);
            $entityManager->flush();

            // Déterminer les destinataires de l'email en fonction du sujet
            $recipients = [];
            if ($contact->getSujet() === 'produit') {
                $recipients = $this->getAdminEmails(); // Implémenter cette méthode pour récupérer tous les emails des administrateurs
            } else {
                $recipients = ['admin@admin.fr']; // Remplacer par l'email de l'administrateur principal
            }

            // Préparer le contexte pour l'email
            $context = [
                'nom' => $contact->getNom(),
                'prenom' => $contact->getPrenom(),
                'user_email' => $contact->getEmail(),
                'sujet' => $contact->getSujet(),
                'message' => $contact->getMessage(),
            ];

            foreach ($recipients as $recipient) {
                $this->emailService->sendContactMail(
                    $contact->getEmail(),
                    $recipient,
                    'Nouveau message de '.$contact->getNom() .' ' .$contact->getPrenom(),
                    'contact', // Assurez-vous d'avoir un template email/contact.html.twig
                    $context
                );
            }

            $this->addFlash('success', 'Votre message a été envoyé avec succès !');
            return $this->redirectToRoute('contact');
        }

        return $this->render('contactForm/contactForm.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}