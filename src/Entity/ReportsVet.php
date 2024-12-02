<?php

namespace App\Entity;

use App\Repository\ReportsVetRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReportsVetRepository::class)]
class ReportsVet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'idReportsVet')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Animals $idAnimals = null;

    #[ORM\ManyToOne(inversedBy: 'idReportsVet')]
    private ?Foods $idFoods = null;

    #[ORM\ManyToOne(inversedBy: 'idReportsVet')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $idUsers = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\ManyToOne(inversedBy: 'idReportsVet')]
    private ?Habitats $idHabitats = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdAnimals(): ?Animals
    {
        return $this->idAnimals;
    }

    public function setIdAnimals(?Animals $idAnimals): static
    {
        $this->idAnimals = $idAnimals;

        return $this;
    }

    public function getIdFoods(): ?Foods
    {
        return $this->idFoods;
    }

    public function setIdFoods(?Foods $idFoods): static
    {
        $this->idFoods = $idFoods;

        return $this;
    }

    public function getIdUsers(): ?Users
    {
        return $this->idUsers;
    }

    public function setIdUsers(?Users $idUsers): static
    {
        $this->idUsers = $idUsers;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getIdHabitats(): ?Habitats
    {
        return $this->idHabitats;
    }

    public function setIdHabitats(?Habitats $idHabitats): static
    {
        $this->idHabitats = $idHabitats;

        return $this;
    }
}
