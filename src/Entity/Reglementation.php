<?php

namespace App\Entity;

use App\Repository\ReglementationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReglementationRepository::class)]
class Reglementation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(length: 255)]
    private ?string $picto = null;

    #[ORM\ManyToMany(targetEntity: Produit::class, mappedBy: 'inforegle')]
    private Collection $typeprod;

    public function __construct()
    {
        $this->typeprod = new ArrayCollection();
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

    public function getPicto(): ?string
    {
        return $this->picto;
    }

    public function setPicto(string $picto): self
    {
        $this->picto = $picto;

        return $this;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getProduit(): Collection
    {
        return $this->typeprod;
    }

    public function addProduit(Produit $typeprod): self
    {
        if (!$this->typeprod->contains($typeprod)) {
            $this->typeprod->add($typeprod);
            $typeprod->addInforegle($this);
        }

        return $this;
    }

    public function removeProduit(Produit $typeprod): self
    {
        if ($this->typeprod->removeElement($typeprod)) {
            $typeprod->removeInforegle($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }
}
