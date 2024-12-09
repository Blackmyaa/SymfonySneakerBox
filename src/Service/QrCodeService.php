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

    /**
     * Créer l'URL mailto avec le sujet et le corps
     * 
     * @param string $email Destinataire de l'email
     * @param string $subject Sujet de l'email
     * @param string $body Corps de l'email
     * @return string URL mailto formatée
     */

    public function generateMailtoUrl(string $email, string $subject, string $body): string
    {
        // Encoder les paramètres pour l'URL
        $subjectEncoded = urlencode($subject);
        $bodyEncoded = urlencode($body);
        
        // Créer l'URL mailto
        return "mailto:$email?subject=$subjectEncoded&body=$bodyEncoded";
    }
}