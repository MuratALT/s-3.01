<?php

namespace App\Entity;

use App\Repository\InfoTechniqueRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InfoTechniqueRepository::class)]
class InfoTechnique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $duree_vie = null;

    #[ORM\Column(length: 255)]
    private ?string $compatibilite = null;

    #[ORM\Column(nullable: true)]
    private ?int $puissSon = null;

    #[ORM\Column]
    private ?float $hauteur = null;

    #[ORM\Column]
    private ?float $largeur = null;

    #[ORM\Column]
    private ?float $longueur = null;

    #[ORM\Column]
    private ?float $profondeur = null;

    #[ORM\Column]
    private ?float $poids = null;

    #[ORM\ManyToOne(inversedBy: 'infoTechniques')]
    private ?TypeAlim $infoalim = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDureeVie(): ?string
    {
        return $this->duree_vie;
    }

    public function setDureeVie(string $duree_vie): self
    {
        $this->duree_vie = $duree_vie;

        return $this;
    }

    public function getCompatibilite(): ?string
    {
        return $this->compatibilite;
    }

    public function setCompatibilite(string $compatibilite): self
    {
        $this->compatibilite = $compatibilite;

        return $this;
    }
    public function getPuissSon(): ?int
    {
        return $this->puissSon;
    }

    public function setPuissSon(int $puissSon): self
    {
        $this->puissSon = $puissSon;

        return $this;
    }

    public function getHauteur(): ?float
    {
        return $this->hauteur;
    }

    public function setHauteur(float $hauteur): self
    {
        $this->hauteur = $hauteur;

        return $this;
    }

    public function getLargeur(): ?float
    {
        return $this->largeur;
    }

    public function setLargeur(float $largeur): self
    {
        $this->largeur = $largeur;

        return $this;
    }

    public function getLongueur(): ?float
    {
        return $this->longueur;
    }

    public function setLongueur(float $longueur): self
    {
        $this->longueur = $longueur;

        return $this;
    }

    public function getProfondeur(): ?float
    {
        return $this->profondeur;
    }

    public function setProfondeur(float $profondeur): self
    {
        $this->profondeur = $profondeur;

        return $this;
    }

    public function getPoids(): ?float
    {
        return $this->poids;
    }

    public function setPoids(float $poids): self
    {
        $this->poids = $poids;

        return $this;
    }

    public function getInfoalim(): ?TypeAlim
    {
        return $this->infoalim;
    }

    public function setInfoalim(?TypeAlim $infoalim): self
    {
        $this->infoalim = $infoalim;

        return $this;
    }
}
