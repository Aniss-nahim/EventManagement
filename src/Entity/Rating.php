<?php

namespace App\Entity;

use App\Repository\RatingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RatingRepository::class)
 */
class Rating
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $ratingScore;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="ratings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $critic;

    /**
     * @ORM\ManyToOne(targetEntity=Event::class, inversedBy="ratings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $criticSubject;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRatingScore(): ?float
    {
        return $this->ratingScore;
    }

    public function setRatingScore(float $ratingScore): self
    {
        $this->ratingScore = $ratingScore;

        return $this;
    }

    public function getCritic(): ?User
    {
        return $this->critic;
    }

    public function setCritic(?User $critic): self
    {
        $this->critic = $critic;

        return $this;
    }

    public function getCriticSubject(): ?Event
    {
        return $this->criticSubject;
    }

    public function setCriticSubject(?Event $criticSubject): self
    {
        $this->criticSubject = $criticSubject;

        return $this;
    }
}
