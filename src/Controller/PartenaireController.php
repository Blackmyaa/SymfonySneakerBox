<?php

namespace App\Controller;

use App\Service\QrCodeService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PartenaireController extends AbstractController
{
    private QrCodeService $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    #[Route('/partenaire', name: 'app_partenaire')]
    public function index(Request $request): Response
    {
        $qrCodesData = [ 
            ['url' => 'https://micreationsrx.sumupstore.com/', 'name' => 'Web'], 
            ['url' => 'https://www.instagram.com/micreationsrx?igsh=MXUxOG9za3RqajhvOA==', 'name' => 'Instagram'], 
            ['url' => 'https://www.facebook.com/share/1EodBhxg5e/', 'name' =>'Facebook'],
            
            // Ajoutez d'autres paires URL-Nom ici 
        ]; 
        
        // Générer les QR codes en base64
        $qrCodesBase64 = [];

        foreach ($qrCodesData as $qrData) {
            $qrCodesBase64[] = [
                'name' => $qrData['name'],
                'image' => 'data:image/png;base64,' . $this->qrCodeService->generateQrCodeBase64($qrData['url'])
            ];
        }

        return $this->render('partenaire/partenaire.html.twig', [
            'qrCodes' => $qrCodesBase64,
        ]);
    }
}
