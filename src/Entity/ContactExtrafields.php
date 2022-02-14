<?php

namespace App\Entity;

use App\Repository\ContactExtrafieldsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ContactExtrafieldsRepository::class)
 */
class ContactExtrafields
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, options={"default": "text"})
     */
    private $input_type;

    /**
     * @ORM\Column(type="string", length=50, options={"default": "Default Title"})
     */
    private $label;

    /**
     * @ORM\Column(type="boolean", options={"default": 1})
     */
    private $for_company;

    /**
     * @ORM\Column(type="text")
     */
    private $extra;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInputType(): ?string
    {
        return $this->input_type;
    }

    public function setInputType(string $input_type): self
    {
        $this->input_type = $input_type;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getForCompany(): ?bool
    {
        return $this->for_company;
    }

    public function setForCompany(bool $for_company): self
    {
        $this->for_company = $for_company;

        return $this;
    }

    public function getExtra(): ?string
    {
        return $this->extra;
    }

    public function setExtra(string $extra): self
    {
        $this->extra = $extra;

        return $this;
    }
}
