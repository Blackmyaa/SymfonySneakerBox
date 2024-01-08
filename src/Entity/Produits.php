<?php

namespace App\Entity;

use App\Repository\ProduitsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Trait\CreatedAtTrait;
use App\Entity\Trait\SlugTrait;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ProduitsRepository::class)]
class Produits
{
    use CreatedAtTrait;
    use SlugTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom du produit ne peut pas être vide')] //Contrainte pour obliger à mettre un nom meme si l'utilisateur bidouille la console
    #[Assert\Length(
        min: 10,
        max: 75,
        minMessage: 'Le titre doit faire au moins {{ limit }} caractères',
        maxMessage:'Le titre ne doit pas dépasser {{ limit }} caractères'
    )]
    private ?string $nom = null;


    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'La description du produit ne peut pas être vide')] //Contrainte pour obliger à mettre une description meme si l'utilisateur bidouille la console
    #[Assert\Length(
        min: 30,
        max: 1500,
        minMessage: 'Le description doit faire au moins {{ limit }} caractères',
        maxMessage:'Le description ne doit pas dépasser {{ limit }} caractères'
    )]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\PositiveOrZero(message: 'Le prix ne peut pas être négatif')]
    private ?int $prix = null;

    #[ORM\Column]
    #[Assert\PositiveOrZero(message: 'Le stock ne peut pas être négatif')]
    private ?int $stock = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categories $categories = null;

    #[ORM\OneToMany(mappedBy: 'produit', cascade:['persist'], targetEntity: Images::class, orphanRemoval: true)]
    private Collection $images;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: DetailCommande::class)]
    private Collection $detailCommandes;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->detailCommandes = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function getCategories(): ?Categories
    {
        return $this->categories;
    }

    public function setCategories(?Categories $categories): static
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return Collection<int, Images>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Images $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setProduit($this);
        }

        return $this;
    }

    public function removeImage(Images $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getProduit() === $this) {
                $image->setProduit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DetailCommande>
     */
    public function getDetailCommandes(): Collection
    {
        return $this->detailCommandes;
    }

    public function addDetailCommande(DetailCommande $detailCommande): static
    {
        if (!$this->detailCommandes->contains($detailCommande)) {
            $this->detailCommandes->add($detailCommande);
            $detailCommande->setProduit($this);
        }

        return $this;
    }

    public function removeDetailCommande(DetailCommande $detailCommande): static
    {
        if ($this->detailCommandes->removeElement($detailCommande)) {
            // set the owning side to null (unless already changed)
            if ($detailCommande->getProduit() === $this) {
                $detailCommande->setProduit(null);
            }
        }

        return $this;
    }
}
