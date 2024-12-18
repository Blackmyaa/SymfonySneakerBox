<?php

// src/Controller/ContactController.php
namespace App\Controller;

use App\Entity\Users;
use App\Entity\Contact;
use App\Form\ContactFormType;
use App\Service\QrCodeService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FooterController extends AbstractController
{
    private $roleHierarchy;
    private $userRepository;
    private $emailService;

    public function __construct(QrCodeService $qrCodeService, RoleHierarchyInterface $roleHierarchy, EntityManagerInterface $entityManager, SendMailService $emailService)
    {
        $this->roleHierarchy = $roleHierarchy;
        $this->userRepository = $entityManager->getRepository(Users::class);
        $this->emailService = $emailService;
        $this->qrCodeService = $qrCodeService;

    }

    private function getAdminEmails()
    {
        // Utiliser le repository injecté pour obtenir tous les utilisateurs
        $users = $this->userRepository->findAll();
        $adminEmails = [];
        foreach ($users as $user) {
            if (in_array("ROLE_ADMIN", $user->getRoles()) || in_array('ROLE_PRODUCT_ADMIN', $user->getRoles())) { 
                $adminEmails[] = $user->getEmail(); 
            }
        }
        
        return $adminEmails;
    }

    private QrCodeService $qrCodeService;

    public function generateMailToQrCode()
    {
        
        // Maintenant vous pouvez utiliser $qrCodeBase64 dans votre application (affichage dans une image par exemple)
        return $qrCodeBase64;
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
                $recipients = ['vinted.ym@gmail.com']; // Remplacer par l'email de l'administrateur principal
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
                $this->emailService->send(
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

        return $this->render('footer/contactForm.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/notreTeam", name:"teamMSB")]
    public function team(): Response
    {
        // Paramètres de l'email
        $email = "ymbenzedi@gmail.com";
        $subject = "Demande de renseignement";
        $body = "Bonjour Yves, j'aimerais obtenir plus d'informations sur certains produits.";

        // Générer l'URL mailto
        $mailtoUrl = $this->qrCodeService->generateMailtoUrl($email, $subject, $body);
        
        // Remplacer les "+" par "%20" pour les espaces dans l'URL
        $mailtoUrl = str_replace('+', '%20', $mailtoUrl);        
        // Générer le QR code en base64
        $qrCodeBase64 = $this->qrCodeService->generateQrCodeBase64($mailtoUrl);
        
        return $this->render('footer/notreTeam.html.twig', [
            'qr_code_base64' => $qrCodeBase64
        ]);
    }
}