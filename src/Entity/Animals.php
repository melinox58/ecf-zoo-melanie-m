<?php

namespace App\Entity;

use App\Repository\AnimalsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnimalsRepository::class)]
class Animals
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    private ?string $nameAnimal = null;

    #[ORM\Column(length: 80)]
    private ?string $breed = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $counter = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Habitats $idHabitats = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameAnimal(): ?string
    {
        return $this->nameAnimal;
    }

    public function setNameAnimal(string $nameAnimal): static
    {
        $this->nameAnimal = $nameAnimal;

        return $this;
    }

    public function getBreed(): ?string
    {
        return $this->breed;
    }

    public function setBreed(string $breed): static
    {
        $this->breed = $breed;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCounter(): ?int
    {
        return $this->counter;
    }

    public function setCounter(int $counter): static
    {
        $this->counter = $counter;

        return $this;
    }

    public function __construct()
    {
        $this->counter = 0; // Initialiser à 0 par défaut
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
