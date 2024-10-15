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
    #[ORM\JoinColumn(nullable: false)]
    private ?Services $idServices = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Animals $idAnimals = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Habitats $idHabitats = null;

    #[ORM\Column(length: 40)]
    private ?string $name = null;

    #[ORM\Column(type: Types::BLOB)]
    private $src;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSrc()
    {
        return $this->src;
    }

    public function setSrc($src): static
    {
        $this->src = $src;

        return $this;
    }
}
