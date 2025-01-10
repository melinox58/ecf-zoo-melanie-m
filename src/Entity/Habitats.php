<?php

namespace App\Entity;

use App\Repository\HabitatsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Images;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: HabitatsRepository::class)]
class Habitats
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'idHabitats', targetEntity: Images::class, cascade: ['persist', 'remove'])]
    private Collection $images;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $state = null;

    /**
     * @var Collection<int, ReportsVet>
     */
    #[ORM\OneToMany(targetEntity: ReportsVet::class, mappedBy: 'idHabitats')]
    private Collection $idReportsVet;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->idReportsVet = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    // Getters et Setters (comme mentionné précédemment)
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Images $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setIdHabitats($this);
        }

        return $this;
    }

    public function removeImage(Images $image): static
    {
        if ($this->images->removeElement($image)) {
            if ($image->getIdHabitats() === $this) {
                $image->setIdHabitats(null);
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
            $idReportsVet->setIdHabitats($this);
        }

        return $this;
    }

    public function removeIdReportsVet(ReportsVet $idReportsVet): static
    {
        if ($this->idReportsVet->removeElement($idReportsVet)) {
            // set the owning side to null (unless already changed)
            if ($idReportsVet->getIdHabitats() === $this) {
                $idReportsVet->setIdHabitats(null);
            }
        }

        return $this;
    }
}
