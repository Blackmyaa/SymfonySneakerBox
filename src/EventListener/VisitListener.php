<?php

namespace App\EventListener;

use App\Entity\Visit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class VisitListener
{
    private $entityManager;
    private $security;
    private $session;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, Security $security, SessionInterface $session)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->session = $session;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $this->logger->info('Nouvelle visite enregistrée');
        if (!$event->isMasterRequest()) {
            return;
        }

        $user = $this->security->getUser();
        
        $visit = new Visit();
        $visit->setDate(new \DateTime());
        $visit->setConnected($user !== null);
        $visit->setPageVisit(true);
        
        // Marque une visite avec une connexion ultérieure si l'utilisateur se connecte après une première visite déconnectée
        if ($user !== null && $this->session->get('visited') === true) {
            $visit->setLoginAfterVisit(true);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $this->entityManager->persist($visit);
        $this->entityManager->flush();

        // Marque la session comme ayant visité la page
        if ($user === null) {
            $this->session->set('visited', true);
        }
    }
}
