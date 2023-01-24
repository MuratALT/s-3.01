<?php

namespace App\Entity;

use App\Repository\PieceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PieceRepository::class)]
class Piece
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(nullable: true)]
    private ?float $hauteur = null;

    #[ORM\Column(nullable: true)]
    private ?float $poids = null;

    #[ORM\Column(nullable: true)]
    private ?float $longueur = null;

    #[ORM\Column(nullable: true)]
    private ?float $profondeur = null;

    #[ORM\OneToMany(mappedBy: 'piece', targetEntity: ProduitPiece::class)]
    private Collection $produitPieces;


    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->produits = new ArrayCollection();
        $this->produitPieces = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getHauteur(): ?float
    {
        return $this->hauteur;
    }

    public function setHauteur(?float $hauteur): self
    {
        $this->hauteur = $hauteur;

        return $this;
    }

    public function getPoids(): ?float
    {
        return $this->poids;
    }

    public function setPoids(?float $poids): self
    {
        $this->poids = $poids;

        return $this;
    }

    public function getLongueur(): ?float
    {
        return $this->longueur;
    }

    public function setLongueur(?float $longueur): self
    {
        $this->longueur = $longueur;

        return $this;
    }

    public function getProfondeur(): ?float
    {
        return $this->profondeur;
    }

    public function setProfondeur(?float $profondeur): self
    {
        $this->profondeur = $profondeur;

        return $this;
    }

    /**
     * @return Collection<int, ProduitPiece>
     */
    public function getProduitPieces(): Collection
    {
        return $this->produitPieces;
    }

    public function addProduitPiece(ProduitPiece $produitPiece): self
    {
        if (!$this->produitPieces->contains($produitPiece)) {
            $this->produitPieces->add($produitPiece);
            $produitPiece->setPiece($this);
        }

        return $this;
    }

    public function removeProduitPiece(ProduitPiece $produitPiece): self
    {
        if ($this->produitPieces->removeElement($produitPiece)) {
            // set the owning side to null (unless already changed)
            if ($produitPiece->getPiece() === $this) {
                $produitPiece->setPiece(null);
            }
        }

        return $this;
    }
}
