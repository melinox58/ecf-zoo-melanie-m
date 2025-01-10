<?php

namespace App\Entity;

use App\Repository\FoodsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FoodsRepository::class)]
class Foods
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $weight = null;

    #[ORM\Column(length: 4)]
    private ?string $unit = null;

    /**
     * @var Collection<int, ReportsVet>
     */
    #[ORM\OneToMany(targetEntity: ReportsVet::class, mappedBy: 'idFoods')]
    private Collection $idReportsVet;

    public function __construct()
    {
        $this->idReportsVet = new ArrayCollection();
    }

    // #[ORM\ManyToOne()]
    // #[ORM\JoinColumn(nullable: false)]
    // private ?Reports $report = null;

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

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): static
    {
        $this->unit = $unit;

        return $this;
    }

    // public function getReport(): ?Reports
    // {
    //     return $this->report;
    // }

    // public function setReport(?Reports $report): static
    // {
    //     $this->report = $report;

    //     return $this;
    // }

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
            $idReportsVet->setIdFoods($this);
        }

        return $this;
    }

    public function removeIdReportsVet(ReportsVet $idReportsVet): static
    {
        if ($this->idReportsVet->removeElement($idReportsVet)) {
            // set the owning side to null (unless already changed)
            if ($idReportsVet->getIdFoods() === $this) {
                $idReportsVet->setIdFoods(null);
            }
        }

        return $this;
    }
}
