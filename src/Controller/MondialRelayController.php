<?php

// src/Controller/MondialRelayController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Service\MondialRelayService;

class MondialRelayController extends AbstractController
{
    private $mondialRelayService;

    public function __construct(MondialRelayService $mondialRelayService)
    {
        $this->mondialRelayService = $mondialRelayService;
    }

    public function findDeliveryPoints(): Response
    {
        $shops = $this->mondialRelayService->findDeliveryPoints([
            'Country' => 'FR',
            'City' => 'Paris',
            'Postcode' => '75000',
            'DelaiEnvoi' => '0',
            'SearchRadius' => '20',
            'ResultsMax' => '10',
        ]);

        return new Response(json_encode($shops));
    }
}
