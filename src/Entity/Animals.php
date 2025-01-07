<?php

namespace App\Entity;

use App\Repository\AnimalsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use App\Entity\Images;
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

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Habitats $idHabitats = null;

    #[ORM\OneToMany(mappedBy: 'idAnimals', targetEntity: Reports::class, cascade: ['persist', 'remove'])]
    private Collection $report;

    // Relation OneToMany avec Images
    #[ORM\OneToMany(mappedBy: 'idAnimals', targetEntity: Images::class, cascade: ['persist', 'remove'])]
    private $images;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $state = null;


    /**
     * @var Collection<int, ReportsVet>
     */
    #[ORM\OneToMany(targetEntity: ReportsVet::class, mappedBy: 'idAnimals')]
    private Collection $idReportsVet;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->report = new ArrayCollection(); // Initialiser la collection des rapports
        $this->idReportsVet = new ArrayCollection();
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

    // Getter et setter pour la collection d'images
    public function getImages(): Collection
    {
        return $this->images;
    }

    // Ajouter une image à la collection
    public function addImage(Images $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setIdAnimals($this); // Associer l'animal à l'image
        }

        return $this;
    }

    // Supprimer une image de la collection
    public function removeImage(Images $image): static
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // Supprimer l'association à l'animal si nécessaire
            if ($image->getIdAnimals() === $this) {
                $image->setIdAnimals(null);
            }
        }

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): static
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return Collection<int, ReportsVet>
     */
    public function getIdReportsVet(): Collection
    {
        return $this->idReportsVet;
    }

    public function addIdReportsVet(ReportsVet $idReportsVet): static
    {
        if (!$this->idReportsVet->contains($idReportsVet)) {
            $this->idReportsVet->add($idReportsVet);
            $idReportsVet->setIdAnimals($this);
        }

        return $this;
    }

    public function removeIdReportsVet(ReportsVet $idReportsVet): static
    {
        if ($this->idReportsVet->removeElement($idReportsVet)) {
            // set the owning side to null (unless already changed)
            if ($idReportsVet->getIdAnimals() === $this) {
                $idReportsVet->setIdAnimals(null);
            }
        }

        return $this;
    }
}
