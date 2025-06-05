<?php

namespace App\Entity;

use App\Repository\HighscoreRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HighscoreRepository::class)]

/**
 * Entity of Highscore
 */
class Highscore
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private int $score;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $created;

    #[ORM\ManyToOne(targetEntity: PlayerEntity::class)]
    #[ORM\JoinColumn(nullable: false)]
    private PlayerEntity $player;

    /**
     * Get id of highscore
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get score of highscore
     *
     * @return int
     */
    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * Set id of highscore
     *
     * @return self
     */
    public function setScore(int $score): self
    {
        $this->score = $score;
        return $this;
    }

    /**
     * Get date of creation of highscore
     */
    public function getCreated(): \DateTimeInterface
    {
        return $this->created;
    }

    /**
     * Set date of creation of highscore
     */
    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;
        return $this;
    }

    /**
     * Get the player of highscore
     */
    public function getPlayer(): PlayerEntity
    {
        return $this->player;
    }

    /**
     * Set the player of highscore
     */
    public function setPlayer(PlayerEntity $player): self
    {
        $this->player = $player;
        return $this;
    }

}
