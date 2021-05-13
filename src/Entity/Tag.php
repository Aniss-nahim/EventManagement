<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TagRepository::class)
 */
class Tag
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tagName;

    /**
     * @ORM\OneToMany(targetEntity=EventTag::class, mappedBy="tag", orphanRemoval=true)
     */
    private $eventTags;

    public function __construct()
    {
        $this->eventTags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTagName(): ?string
    {
        return $this->tagName;
    }

    public function setTagName(string $tagName): self
    {
        $this->tagName = $tagName;

        return $this;
    }

    /**
     * @return Collection|EventTag[]
     */
    public function getEventTags(): Collection
    {
        return $this->eventTags;
    }

    public function addEventTag(EventTag $eventTag): self
    {
        if (!$this->eventTags->contains($eventTag)) {
            $this->eventTags[] = $eventTag;
            $eventTag->setTaggedEvent($this);
        }

        return $this;
    }

    public function removeEventTag(EventTag $eventTag): self
    {
        if ($this->eventTags->removeElement($eventTag)) {
            // set the owning side to null (unless already changed)
            if ($eventTag->getTaggedEvent() === $this) {
                $eventTag->setTaggedEvent(null);
            }
        }

        return $this;
    }
}
