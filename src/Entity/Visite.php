<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\VisiteRepository;

#[ORM\Entity(repositoryClass: VisiteRepository::class)]
class Visite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;

    #[ORM\Column]
    private ?string $ipAddress;

    #[ORM\Column]
    private ?string $visitedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    public function getVisitedAt(): ?string
    {
        return $this->visitedAt;
    }

    public function setVisitedAt(string $visitedAt): self
    {
        $this->visitedAt = $visitedAt; 
        return $this;
    }
}
