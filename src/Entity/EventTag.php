<?php

namespace App\Entity;

use App\Repository\EventTagRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EventTagRepository::class)
 */
class EventTag
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("event:read")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Tag::class, inversedBy="eventTags")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("event:read")
     */
    private $tag;

    /**
     * @ORM\ManyToOne(targetEntity=Event::class, inversedBy="eventTags")
     * @ORM\JoinColumn(nullable=false)
     */
    private $taggedEvent;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTag(): ?Tag
    {
        return $this->tag;
    }

    public function setTag(?Tag $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    public function getTaggedEvent(): ?Event
    {
        return $this->taggedEvent;
    }

    public function setTaggedEvent(?Event $taggedEvent): self
    {
        $this->taggedEvent = $taggedEvent;

        return $this;
    }
}
