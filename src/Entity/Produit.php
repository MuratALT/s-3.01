<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]

class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $pu = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?InfoTechnique $infotech = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?InfoMarketing $infomarket = null;

    #[ORM\ManyToMany(targetEntity: Reglementation::class, inversedBy: 'typeprod')]
    private Collection $inforegle;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    private ?TypeProd $typeprod = null;

    #[ORM\Column(length: 255)]
    private ?string $garantie = null;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: Ticket::class)]
    private Collection $tickets;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column]
    private ?int $reference = null;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: Images::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $images;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: DocumentProduit::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $documentProduits;

    #[ORM\Column(nullable: true)]
    private ?bool $isArchived = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $labelDE = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $labelEN = null;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: ProduitPiece::class)]
    private Collection $produitPieces;

    public function __construct()
    {
        $this->inforegle = new ArrayCollection();
        $this->tickets = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->test = new ArrayCollection();
        $this->documentProduits = new ArrayCollection();
        $this->pieces = new ArrayCollection();
        $this->produitPieces = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPu(): ?float
    {
        return $this->pu;
    }

    public function setPu(float $pu): self
    {
        $this->pu = $pu;

        return $this;
    }

    public function getInfotech(): ?InfoTechnique
    {
        return $this->infotech;
    }

    public function setInfotech(InfoTechnique $infotech): self
    {
        $this->infotech = $infotech;

        return $this;
    }

    public function getInfomarket(): ?InfoMarketing
    {
        return $this->infomarket;
    }

    public function setInfomarket(InfoMarketing $infomarket): self
    {
        $this->infomarket = $infomarket;

        return $this;
    }

    /**
     * @return Collection<int, Reglementation>
     */
    public function getInforegle(): Collection
    {
        return $this->inforegle;
    }

    public function addInforegle(Reglementation $inforegle): self
    {
        if (!$this->inforegle->contains($inforegle)) {
            $this->inforegle->add($inforegle);
        }

        return $this;
    }

    public function removeInforegle(Reglementation $inforegle): self
    {
        $this->inforegle->removeElement($inforegle);

        return $this;
    }

    public function getTypeprod(): ?TypeProd
    {
        return $this->typeprod;
    }

    public function setTypeprod(?TypeProd $typeprod): self
    {
        $this->typeprod = $typeprod;

        return $this;
    }

    public function getGarantie(): ?string
    {
        return $this->garantie;
    }

    public function setGarantie(string $garantie): self
    {
        $this->garantie = $garantie;

        return $this;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): self
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setProduit($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getProduit() === $this) {
                $ticket->setProduit(null);
            }
        }

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

    public function getReference(): ?int
    {
        return $this->reference;
    }

    public function setReference(int $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * @return Collection<int, Images>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Images $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setProduit($this);
        }

        return $this;
    }

    public function removeImage(Images $image): self
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
     * @return Collection<int, DocumentProduit>
     */
    public function getDocumentProduits(): Collection
    {
        return $this->documentProduits;
    }

    public function addDocumentProduit(DocumentProduit $documentProduit): self
    {
        if (!$this->documentProduits->contains($documentProduit)) {
            $this->documentProduits->add($documentProduit);
            $documentProduit->setProduit($this);
        }

        return $this;
    }

    public function removeDocumentProduit(DocumentProduit $documentProduit): self
    {
        if ($this->documentProduits->removeElement($documentProduit)) {
            // set the owning side to null (unless already changed)
            if ($documentProduit->getProduit() === $this) {
                $documentProduit->setProduit(null);
            }
        }

        return $this;
    }

    public function isIsArchived(): ?bool
    {
        return $this->isArchived;
    }

    public function setIsArchived(bool $isArchived): self
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    public function getLabelDE(): ?string
    {
        return $this->labelDE;
    }

    public function setLabelDE(?string $labelDE): self
    {
        $this->labelDE = $labelDE;

        return $this;
    }

    public function getLabelEN(): ?string
    {
        return $this->labelEN;
    }

    public function setLabelEN(?string $labelEN): self
    {
        $this->labelEN = $labelEN;

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
            $produitPiece->setProduit($this);
        }

        return $this;
    }

    public function removeProduitPiece(ProduitPiece $produitPiece): self
    {
        if ($this->produitPieces->removeElement($produitPiece)) {
            // set the owning side to null (unless already changed)
            if ($produitPiece->getProduit() === $this) {
                $produitPiece->setProduit(null);
            }
        }

        return $this;
    }


}
