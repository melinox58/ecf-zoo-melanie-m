<?php

namespace App\Entity;

use App\Repository\ImagesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImagesRepository::class)]
class Images
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Services $idServices = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Animals $idAnimals = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Habitats $idHabitats = null;


    // Remplacement du champ src (BLOB) par filePath pour stocker le chemin du fichier
    #[ORM\Column(type: "string", length: 255)]
    private ?string $filePath = null;

    // Getters et setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdServices(): ?Services
    {
        return $this->idServices;
    }

    public function setIdServices(?Services $idServices): static
    {
        $this->idServices = $idServices;
        return $this;
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

    public function getIdHabitats(): ?Habitats
    {
        return $this->idHabitats;
    }

    public function setIdHabitats(?Habitats $idHabitats): static
    {
        $this->idHabitats = $idHabitats;
        return $this;
    }

    // Getter et setter pour le champ filePath (chemin du fichier)
    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(string $filePath): static
    {
        $this->filePath = $filePath;
        return $this;
    }
}
