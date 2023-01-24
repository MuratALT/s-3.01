<?php

namespace App\Entity;

use App\Repository\InfoMarketingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InfoMarketingRepository::class)]
class InfoMarketing
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 1000)]
    private ?string $description = null;

    #[ORM\Column(length: 1000)]
    private ?string $fonctionnalites = null;

    #[ORM\Column(length: 1000)]
    private ?string $benefices = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getFonctionnalites(): ?string
    {
        return $this->fonctionnalites;
    }

    public function setFonctionnalites(string $fonctionnalites): self
    {
        $this->fonctionnalites = $fonctionnalites;

        return $this;
    }

    public function getBenefices(): ?string
    {
        return $this->benefices;
    }

    public function setBenefices(string $benefices): self
    {
        $this->benefices = $benefices;

        return $this;
    }
}
