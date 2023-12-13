<?php
// On crée un trait pour factoriser le code, on met dans un trait un bout de code qui est répété plusieurs fois afin d'alléger le code

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;

trait CreatedAtTrait
{
    #[ORM\Column(options: ['default'=> 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $created_at = null;
    
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }
}
