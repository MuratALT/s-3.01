<?php

namespace App\Entity;

use App\Repository\VenteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VenteRepository::class)]
class Vente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ventes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $categorie = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(length: 255)]
    private ?string $commentaire = null;

    #[ORM\OneToMany(mappedBy: 'vente', targetEntity: DocumentVente::class, cascade: ['persist'])]
    private Collection $documentVentes;

    public function __construct()
    {
        $this->documentVentes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
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

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * @return Collection<int, DocumentVente>
     */
    public function getDocumentVentes(): Collection
    {
        return $this->documentVentes;
    }

    public function addDocumentVente(DocumentVente $documentVente): self
    {
        if (!$this->documentVentes->contains($documentVente)) {
            $this->documentVentes->add($documentVente);
            $documentVente->setVente($this);
        }

        return $this;
    }

    public function removeDocumentVente(DocumentVente $documentVente): self
    {
        if ($this->documentVentes->removeElement($documentVente)) {
            // set the owning side to null (unless already changed)
            if ($documentVente->getVente() === $this) {
                $documentVente->setVente(null);
            }
        }

        return $this;
    }
}
