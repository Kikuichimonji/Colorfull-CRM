<?php

namespace App\Entity;

use App\Repository\ContactExtrafieldValueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ContactExtrafieldValueRepository::class)
 */
class ContactExtrafieldValue
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity=ContactExtrafields::class, inversedBy="contactExtrafieldValue")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contactExtrafield;

    /**
     * @ORM\ManyToOne(targetEntity=Contact::class, inversedBy="contactExtrafieldValue")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contact;

    public function __construct()
    {

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getContactExtrafield(): ?ContactExtrafields
    {
        return $this->contactExtrafield;
    }

    public function setContactExtrafield(?ContactExtrafields $contactExtrafield): self
    {
        $this->contactExtrafield = $contactExtrafield;

        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): self
    {
        $this->contact = $contact;

        return $this;
    }
}
