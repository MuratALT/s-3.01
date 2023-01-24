<?php

namespace App\Entity;

use App\Repository\LastPasswordRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LastPasswordRepository::class)]
class LastPassword
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false, onDelete:"CASCADE")]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $password1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $password2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $password3 = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPassword1(): ?string
    {
        return $this->password1;
    }

    public function setPassword1(?string $password1): self
    {
        $this->password1 = $password1;

        return $this;
    }

    public function getPassword2(): ?string
    {
        return $this->password2;
    }

    public function setPassword2(?string $password2): self
    {
        $this->password2 = $password2;

        return $this;
    }

    public function getPassword3(): ?string
    {
        return $this->password3;
    }

    public function setPassword3(?string $password3): self
    {
        $this->password3 = $password3;

        return $this;
    }
}
