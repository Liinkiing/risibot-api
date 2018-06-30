<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"api"})
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Risipic", mappedBy="category")
     */
    private $risipics;

    public function __construct()
    {
        $this->risipics = new ArrayCollection();
    }

    public function getId()
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
     * @return Collection|Risipic[]
     */
    public function getRisipics(): Collection
    {
        return $this->risipics;
    }

    public function addRisipic(Risipic $risipic): self
    {
        if (!$this->risipics->contains($risipic)) {
            $this->risipics[] = $risipic;
            $risipic->setCategory($this);
        }

        return $this;
    }

    public function removeRisipic(Risipic $risipic): self
    {
        if ($this->risipics->contains($risipic)) {
            $this->risipics->removeElement($risipic);
            // set the owning side to null (unless already changed)
            if ($risipic->getCategory() === $this) {
                $risipic->setCategory(null);
            }
        }

        return $this;
    }
}
