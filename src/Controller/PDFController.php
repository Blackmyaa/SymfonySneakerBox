<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Repository\UsersRepository;
use App\Repository\CommandesRepository;
use App\Repository\DetailCommandeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PDFController extends AbstractController
{
    private $usersRepository;
    private $commandesRepository;
    private $detailCommandeRepository;

    public function __construct(UsersRepository $usersRepository, CommandesRepository $commandesRepository, DetailCommandeRepository $detailCommandeRepository)
    {
        $this->usersRepository = $usersRepository;
        $this->commandesRepository = $commandesRepository;
        $this->detailCommandeRepository = $detailCommandeRepository;
    }

    #[Route('/generate-pdf/{commandeId}', name: 'generate_pdf')]
    public function genererPDF($commandeId): Response
    {

        $commande = $this->commandesRepository->find($commandeId);
        if (!$commande) {
            throw $this->createNotFoundException('Aucune commande trouvée');
        }
        $details = $this->detailCommandeRepository->findBy(['commande' => $commandeId]);
        
        $user = $this->usersRepository->find($commande->getUsers());
        
        $addition = 0;

        foreach ($details as $produit) {
            
            $prix = $produit->getPrix();
            $quantite = $produit->getQuantite();
            
            $addition = ($quantite * $prix) + $addition;
        }
        $addition = $addition / 100;

        // Générer le PDF
        $twig = $this->renderView('orders/facturePDF.html.twig', [
            'user' => $user,
            'commande' => $commande,
            'details' => $details,
            'addition' =>$addition
        ]);


        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($twig);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $pdfContent = $dompdf->output();

        // Préparer la réponse pour le téléchargement du PDF
        $response = new Response(
            $pdfContent,
            200,
            array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="factureMSB_' . $commande->getReference() .'_.pdf"',
            )
        );
        // Envoyer la réponse du PDF

        return $response;
    }
}
