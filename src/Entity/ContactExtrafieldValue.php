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
     * @ORM\Column(type="text")
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity=ContactExtrafieldValue::class, inversedBy="contactExtrafieldValues")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contactExtrafield;

    /**
     * @ORM\OneToMany(targetEntity=ContactExtrafieldValue::class, mappedBy="contactExtrafield", orphanRemoval=true)
     */
    private $contactExtrafieldValues;

    /**
     * @ORM\ManyToOne(targetEntity=Contact::class, inversedBy="contactExtrafieldValues")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contact;

    public function __construct()
    {
        $this->contactExtrafieldValues = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getContactExtrafield(): ?self
    {
        return $this->contactExtrafield;
    }

    public function setContactExtrafield(?self $contactExtrafield): self
    {
        $this->contactExtrafield = $contactExtrafield;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getContactExtrafieldValues(): Collection
    {
        return $this->contactExtrafieldValues;
    }

    public function addContactExtrafieldValue(self $contactExtrafieldValue): self
    {
        if (!$this->contactExtrafieldValues->contains($contactExtrafieldValue)) {
            $this->contactExtrafieldValues[] = $contactExtrafieldValue;
            $contactExtrafieldValue->setContactExtrafield($this);
        }

        return $this;
    }

    public function removeContactExtrafieldValue(self $contactExtrafieldValue): self
    {
        if ($this->contactExtrafieldValues->removeElement($contactExtrafieldValue)) {
            // set the owning side to null (unless already changed)
            if ($contactExtrafieldValue->getContactExtrafield() === $this) {
                $contactExtrafieldValue->setContactExtrafield(null);
            }
        }

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
