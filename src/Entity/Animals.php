<?php

namespace App\Entity;

use App\Repository\AnimalsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\OneToMany(mappedBy: 'idAnimals', targetEntity: Reports::class, cascade: ['persist', 'remove'])]
    private Collection $report;

    public function __construct()
    {
        $this->counter = 0; // Initialiser à 0 par défaut
        $this->report = new ArrayCollection(); // Initialiser la collection des rapports
    }

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

    public function __toString(): string
    {
        return $this->nameAnimal;
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

    public function getIdHabitats(): ?Habitats
    {
        return $this->idHabitats;
    }

    public function setIdHabitats(?Habitats $idHabitats): static
    {
        $this->idHabitats = $idHabitats;

        return $this;
    }

    // Getter pour la collection de rapports
    public function getReports(): Collection
    {
        return $this->report;
    }

    // Ajouter un rapport à la collection
    public function addReport(Reports $report): static
    {
        if (!$this->report->contains($report)) {
            $this->report->add($report);
            $report->setIdAnimals($this); // Associer l'animal au rapport
        }

        return $this;
    }

    // Supprimer un rapport de la collection
    public function removeReport(Reports $report): static
    {
        if ($this->report->contains($report)) {
            $this->report->removeElement($report);
            // Supprimer l'association à l'animal si nécessaire
            if ($report->getIdAnimals() === $this) {
                $report->setIdAnimals(null);
            }
        }

        return $this;
    }
}
