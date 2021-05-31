<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=EventRepository::class)
 */
class Event
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("event:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     * @Groups("event:read")
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=150)
     * @Assert\NotBlank()
     * @Groups("event:read")
     */
    private $type;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Groups("event:read")
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     * @Groups("event:read")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     * @Groups("event:read")
     */
    private $endDate;

    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotBlank()
     * @Groups("event:read")
     */
    private $state = "Created"; // Created - Published - Canceled - Deleted - Re-published(Published then edited)

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     * @Groups("event:read")
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups("event:read")
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups("event:read")
     */
    private $coverImage = "default.png";

    /**
     * @ORM\Column(type="datetime")
     * @Groups("event:read")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Groups("event:read")
     */
    private $updatedAt;

    /**
     * @var float
     */
    private $score = 0;

    /**
     * @ORM\OneToMany(targetEntity=Participation::class, mappedBy="participatedEvent", cascade={"remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(nullable=false)
     */
    private $participations;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="ownedEvents")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("event:read")
     */
    private $owner;

    /**
     * @ORM\OneToMany(targetEntity=Rating::class, mappedBy="ciriticSubject", cascade={"persist", "remove"},  orphanRemoval=true)
     */
    private $ratings;

    /**
     * @ORM\OneToMany(targetEntity=EventTag::class, mappedBy="taggedEvent", cascade={"persist", "remove"},  orphanRemoval=true)
     * @Groups("event:read")
     */
    private $eventTags;

    public function __construct()
    {
        $this->participations = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        $this->eventTags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(string $coverImage): self
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    /**
     * @return Collection|Participation[]
     */
    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function addParticipation(Participation $participation): self
    {
        if (!$this->participations->contains($participation)) {
            $this->participations[] = $participation;
            $participation->setParticipatedEvent($this);
        }

        return $this;
    }

    public function removeParticipation(Participation $participation): self
    {
        if ($this->participations->removeElement($participation)) {
            // set the owning side to null (unless already changed)
            if ($participation->getParticipatedEvent() === $this) {
                $participation->setParticipatedEvent(null);
            }
        }

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection|Rating[]
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function addRating(Rating $rating): self
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings[] = $rating;
            $rating->setCiriticSubject($this);
        }

        return $this;
    }

    public function removeRating(Rating $rating): self
    {
        if ($this->ratings->removeElement($rating)) {
            // set the owning side to null (unless already changed)
            if ($rating->getCiriticSubject() === $this) {
                $rating->setCiriticSubject(null);
            }
        }

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

    public function setScore()
    {
        $count = 0;
        $sum = 0;
        foreach($this->ratings as $rating){
            $count+=1;
            $sum+=$rating->getRatingScore();
        }
        if($count!=0){
            $this->score = round($sum/$count,1);
        }
        else $this->score = 0;
    }

    public function getScore(): float
    {
        $this->setScore();
        return $this->score;
    }
}
