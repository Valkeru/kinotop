<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Movie
 *
 * @package AppBundle\Entity
 *
 * @ORM\Table(name="movie")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MovieRepository")
 */
class Movie
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $position;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $russainName;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private $originalName;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $year;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=false)
     */
    private $rating;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $votersCount;

    /**
     * @var \DateTime
     * @ORM\Column(type="date", nullable=false)
     */
    private $inTopDate;

    public function __construct()
    {
        $this->inTopDate = new \DateTime();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $votersCount
     *
     * @return Movie
     */
    public function setVotersCount(int $votersCount): Movie
    {
        $this->votersCount = $votersCount;

        return $this;
    }

    /**
     * @param int $position
     *
     * @return Movie
     */
    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return string
     */
    public function getRussainName(): string
    {
        return $this->russainName;
    }

    /**
     * @param string $russainName
     *
     * @return Movie
     */
    public function setRussainName(string $russainName): self
    {
        $this->russainName = $russainName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    /**
     * @param string|null $originalName
     *
     * @return Movie
     */
    public function setOriginalName(?string $originalName): self
    {
        $this->originalName = $originalName;

        return $this;
    }

    /**
     * @return float
     */
    public function getRating(): float
    {
        return $this->rating;
    }

    /**
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * @param float $rating
     *
     * @return Movie
     */
    public function setRating(float $rating): Movie
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * @return int
     */
    public function getVotersCount(): int
    {
        return $this->votersCount;
    }

    /**
     * @param int $year
     *
     * @return Movie
     */
    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getInTopDate(): \DateTime
    {
        return $this->inTopDate;
    }

    /**
     * @param \DateTime $inTopDate
     *
     * @return Movie
     */
    public function setInTopDate(\DateTime $inTopDate): self
    {
        $this->inTopDate = $inTopDate;

        return $this;
    }
}

