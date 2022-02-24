<?php

namespace App\Entity;

use App\Repository\ContactRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Annotation\Ignore;


/**
 * @ORM\Entity(repositoryClass=ContactRepository::class)
 */
class Contact
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private $is_company;

    /**
     * @ORM\Column(type="string", length=50, options={"default": "Default Name"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $phone1;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $phone2;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="datetime", options={"default": "2022-01-01 00:00:00"})
     */
    private $created_at;

    /**
     * @ORM\ManyToMany(targetEntity=Event::class, mappedBy="contacts")
     */
    private $events;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="contacts")
     */
    private $userCreate;

    /**
     * @ORM\ManyToMany(targetEntity=ContactType::class, inversedBy="contacts")
     */
    private $contactType;

    /**
     * @ORM\OneToMany(targetEntity=ContactExtrafieldValue::class, mappedBy="contact")
     */
    private $contactExtrafieldValues;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->contact_extrafields = new ArrayCollection();
        $this->contactType = new ArrayCollection();
        $this->contactExtrafieldValues = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsCompany(): ?bool
    {
        return $this->is_company;
    }

    public function setIsCompany(bool $is_company): self
    {
        $this->is_company = $is_company;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPhone1(): ?string
    {
        return $this->phone1;
    }

    public function setPhone1(?string $phone1): self
    {
        $this->phone1 = $phone1;

        return $this;
    }

    public function getPhone2(): ?string
    {
        return $this->phone2;
    }

    public function setPhone2(?string $phone2): self
    {
        $this->phone2 = $phone2;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return Collection|Event[]
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->addContact($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->removeElement($event)) {
            $event->removeContact($this);
        }

        return $this;
    }

    public function getUserCreate(): ?User
    {
        return $this->userCreate;
    }

    public function setUserCreate(?User $userCreate): self
    {
        $this->userCreate = $userCreate;

        return $this;
    }

    /**
     * @return Collection|ContactType[]
     */
    public function getContactType(): Collection
    {
        return $this->contactType;
    }

    public function addContactType(ContactType $contactType): self
    {
        if (!$this->contactType->contains($contactType)) {
            $this->contactType[] = $contactType;
        }

        return $this;
    }

    public function removeContactType(ContactType $contactType): self
    {
        $this->contactType->removeElement($contactType);

        return $this;
    }

    /**
     * @return Collection|ContactExtrafieldValue[]
     */
    public function getContactExtrafieldValues(): Collection
    {
        return $this->contactExtrafieldValues;
    }

    public function addContactExtrafieldValue(ContactExtrafieldValue $contactExtrafieldValue): self
    {
        if (!$this->contactExtrafieldValues->contains($contactExtrafieldValue)) {
            $this->contactExtrafieldValues[] = $contactExtrafieldValue;
            $contactExtrafieldValue->setContact($this);
        }

        return $this;
    }

    public function removeContactExtrafieldValue(ContactExtrafieldValue $contactExtrafieldValue): self
    {
        if ($this->contactExtrafieldValues->removeElement($contactExtrafieldValue)) {
            // set the owning side to null (unless already changed)
            if ($contactExtrafieldValue->getContact() === $this) {
                $contactExtrafieldValue->setContact(null);
            }
        }

        return $this;
    }

}
