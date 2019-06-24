<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CountrySearchRepository")
 */
class CountrySearch
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    public $country_name;

    /**
     * @ORM\Column(type="datetime")
     */
    public $search_date;

    public function __construct()
    {
        $this->search_date = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountryName(): ?string
    {
        return $this->country_name;
    }

    public function setCountryName(?string $name): self
    {
        $this->country_name = $name;

        return $this;
    }

    public function getDateSearch(): ?\DateTimeInterface
    {
        return $this->search_date;
    }

}
