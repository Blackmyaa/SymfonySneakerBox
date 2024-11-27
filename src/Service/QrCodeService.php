<?php

// src/Service/QRCodeService.php
namespace App\Service;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class QrCodeService
{
    private PngWriter $writer;
    
    public function __construct()
    {
        $this->writer = new PngWriter();
    }

    /**
     * Génère un QR code et le retourne en format base64
     */
    public function generateQrCodeBase64(string $data): string
    {
        // Créer un QR code avec les données
        $qrCode = new QrCode($data);
        
        // Générer l'image du QR code
        $result = $this->writer->write($qrCode);
        
        // Récupérer l'image en format PNG et la convertir en base64
        $imageData = $result->getString(); // Données binaires de l'image

        return base64_encode($imageData); // Convertir en base64    
        }
}