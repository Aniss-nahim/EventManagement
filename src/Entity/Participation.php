<?php

namespace App\Entity;

use App\Repository\ParticipationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ParticipationRepository::class)
 */
class Participation
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
    private $type;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="participations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $participantUser;

    /**
     * @ORM\ManyToOne(targetEntity=Event::class, inversedBy="participations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $participatedEvent;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getParticipantUser(): ?User
    {
        return $this->participantUser;
    }

    public function setParticipantUser(?User $participantUser): self
    {
        $this->participantUser = $participantUser;

        return $this;
    }

    public function getParticipatedEvent(): ?Event
    {
        return $this->participatedEvent;
    }

    public function setParticipatedEvent(?Event $participatedEvent): self
    {
        $this->participatedEvent = $participatedEvent;

        return $this;
    }
}
