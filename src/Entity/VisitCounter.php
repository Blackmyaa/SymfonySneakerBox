<?php

namespace App\Entity;

use App\Repository\VisitCounterRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VisitCounterRepository::class)]
class VisitCounter
{ 
    #[ORM\Id] 
    #[ORM\GeneratedValue] 
    #[ORM\Column(type: 'integer')] 
    private $id; 
    
    #[ORM\Column(type: 'datetime')] 
    private $date; 
    
    #[ORM\Column(type: 'boolean')] 
    private $connected; 
    
    #[ORM\Column(type: 'boolean')] 
    private $pageVisit; 
    
    #[ORM\Column(type: 'boolean')] 
    private $loginAfterVisit = false; 
    
    public function getId(): ?int 
    { 
        return $this->id; 
    } 
    
    public function getDate(): ?\DateTimeInterface 
    { 
        return $this->date; 
    } 
    
    public function setDate(\DateTimeInterface $date): self 
    { 
        $this->date = $date; return $this; 
    } 
    
    public function isConnected(): ?bool
    { 
        return $this->connected; 
    } 
    
    public function setConnected(bool $connected): self 
    { 
        $this->connected = connected; return $this; 
    } 
    
    public function isPageVisit(): ?bool 
    { 
        return $this->pageVisit; 
    } 
    
    public function setPageVisit(bool $pageVisit): self 
    {
        $this->pageVisit = pageVisit; return $this; 
    } 
    
    public function isLoginAfterVisit(): ?bool 
    { 
        return $this->loginAfterVisit; 
    } 
    
    public function setLoginAfterVisit(bool $loginAfterVisit): self 
    { 
        $this->loginAfterVisit = loginAfterVisit; return $this; 
    } 
}