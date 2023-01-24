<?php

namespace App\Entity;

use App\Repository\TypeAlimRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeAlimRepository::class)]
class TypeAlim
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'infoalim', targetEntity: InfoTechnique::class)]
    private Collection $infoTechniques;

    public function __construct()
    {
        $this->infoTechniques = new ArrayCollection();
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
            $infoTechnique->setInfoalim($this);
        }

        return $this;
    }

    public function removeInfoTechnique(InfoTechnique $infoTechnique): self
    {
        if ($this->infoTechniques->removeElement($infoTechnique)) {
            // set the owning side to null (unless already changed)
            if ($infoTechnique->getInfoalim() === $this) {
                $infoTechnique->setInfoalim(null);
            }
        }

        return $this;
    }
}
