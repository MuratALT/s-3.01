<?php

namespace App\Entity;

use App\Repository\MediaProduitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MediaProduitRepository::class)]
class MediaProduit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $lien = null;

    #[ORM\ManyToOne(inversedBy: 'mediaProduits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeMedia $typemedia = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Produit $produit = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLien(): ?string
    {
        return $this->lien;
    }

    public function setLien(string $lien): self
    {
        $this->lien = $lien;

        return $this;
    }

    public function getTypemedia(): ?TypeMedia
    {
        return $this->typemedia;
    }

    public function setTypemedia(?TypeMedia $typemedia): self
    {
        $this->typemedia = $typemedia;

        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): self
    {
        $this->produit = $produit;

        return $this;
    }
}
