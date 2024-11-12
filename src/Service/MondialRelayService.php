<?php

// src/Service/MondialRelayService.php
namespace App\Service;

use MondialRelay\Webservice;

class MondialRelayService
{
    private $webservice;

    public function __construct()
    {
        $this->webservice = new Webservice([
            'customerCode' => 'TON_CODE_CLIENT',
            'apiKey' => 'TON_CLE_SECRETE',
        ]);
    }

    public function findDeliveryPoints(array $params)
    {
        return $this->webservice->searchParcelShops($params);
    }
}