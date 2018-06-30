<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RisipicRepository")
 */
class Risipic
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
    private $url;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="risipics", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"api"})
     */
    private $category;

    /**
     * @ORM\Column(type="array", nullable=true)
     * @Groups({"api"})
     */
    private $tags;

    /**
     * @ORM\Column(type="string", length=6)
     * @Groups({"api"})
     */
    private $extension;

    public function getId()
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(?array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }
}
