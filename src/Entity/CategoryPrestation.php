<?php

namespace App\Entity;

use App\Repository\CategoryPrestationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryPrestationRepository::class)]
class CategoryPrestation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'categoryPrestation', targetEntity: Prestation::class)]
    private Collection $type;

    public function __construct()
    {
        $this->type = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Prestation>
     */
    public function getType(): Collection
    {
        return $this->type;
    }

    public function addType(Prestation $type): self
    {
        if (!$this->type->contains($type)) {
            $this->type[] = $type;
            $type->setCategoryPrestation($this);
        }

        return $this;
    }

    public function removeType(Prestation $type): self
    {
        if ($this->type->removeElement($type)) {
            // set the owning side to null (unless already changed)
            if ($type->getCategoryPrestation() === $this) {
                $type->setCategoryPrestation(null);
            }
        }

        return $this;
    }
}
