<?php

namespace App\Entity;

use App\Repository\TypeMediaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeMediaRepository::class)]
class TypeMedia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'typemedia', targetEntity: MediaProduit::class)]
    private Collection $mediaProduits;

    public function __construct()
    {
        $this->mediaProduits = new ArrayCollection();
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
     * @return Collection<int, MediaProduit>
     */
    public function getMediaProduits(): Collection
    {
        return $this->mediaProduits;
    }

    public function addMediaProduit(MediaProduit $mediaProduit): self
    {
        if (!$this->mediaProduits->contains($mediaProduit)) {
            $this->mediaProduits->add($mediaProduit);
            $mediaProduit->setTypemedia($this);
        }

        return $this;
    }

    public function removeMediaProduit(MediaProduit $mediaProduit): self
    {
        if ($this->mediaProduits->removeElement($mediaProduit)) {
            // set the owning side to null (unless already changed)
            if ($mediaProduit->getTypemedia() === $this) {
                $mediaProduit->setTypemedia(null);
            }
        }

        return $this;
    }
}
