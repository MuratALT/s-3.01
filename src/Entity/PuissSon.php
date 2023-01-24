<?php

namespace App\Entity;

use App\Repository\PuissSonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PuissSonRepository::class)]
class PuissSon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $mesure = null;

    #[ORM\OneToMany(mappedBy: 'infoson', targetEntity: InfoTechnique::class)]
    private Collection $infoTechniques;

    public function __construct()
    {
        $this->infoTechniques = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMesure(): ?float
    {
        return $this->mesure;
    }

    public function setMesure(float $mesure): self
    {
        $this->mesure = $mesure;

        return $this;
    }

    /**
     * @return Collection<int, InfoTechnique>
     */
    public function getInfoTechniques(): Collection
    {
        return $this->infoTechniques;
    }

    public function addInfoTechnique(InfoTechnique $infoTechnique): self
    {
        if (!$this->infoTechniques->contains($infoTechnique)) {
            $this->infoTechniques->add($infoTechnique);
            $infoTechnique->setInfoson($this);
        }

        return $this;
    }

    public function removeInfoTechnique(InfoTechnique $infoTechnique): self
    {
        if ($this->infoTechniques->removeElement($infoTechnique)) {
            // set the owning side to null (unless already changed)
            if ($infoTechnique->getInfoson() === $this) {
                $infoTechnique->setInfoson(null);
            }
        }

        return $this;
    }
}
