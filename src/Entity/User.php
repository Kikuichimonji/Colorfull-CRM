<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=70)
     */
    private $last_name;

    /**
     * @ORM\Column(type="string", length=70)
     */
    private $first_name;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $picture;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;


    /**
     * @ORM\OneToOne(targetEntity=Planning::class, mappedBy="planning_owner", cascade={"persist", "remove"})
     */
    private $planning;

    /**
     * @ORM\ManyToMany(targetEntity=Planning::class, inversedBy="users")
     */
    private $plannings;

    /**
     * @ORM\ManyToMany(targetEntity=Event::class, inversedBy="users")
     */
    private $events;

    /**
     * @ORM\OneToMany(targetEntity=Contact::class, mappedBy="userCreate")
     */
    private $contacts;

    /**
     * @ORM\ManyToMany(targetEntity=Team::class, inversedBy="users")
     */
    private $invitedTeams;

    /**
     * @ORM\OneToMany(targetEntity=Team::class, mappedBy="user")
     */
    private $ownedTeams;


    public function __construct()
    {
        $this->teams = new ArrayCollection();
        $this->plannings = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->contacts = new ArrayCollection();
        $this->ownedTeams = new ArrayCollection();
        $this->invitedTeams = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles($roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getPlanning(): ?Planning
    {
        return $this->planning;
    }

    public function setPlanning(?Planning $planning): self
    {
        // unset the owning side of the relation if necessary
        if ($planning === null && $this->planning !== null) {
            $this->planning->setPlanningOwner(null);
        }

        // set the owning side of the relation if necessary
        if ($planning !== null && $planning->getPlanningOwner() !== $this) {
            $planning->setPlanningOwner($this);
        }

        $this->planning = $planning;

        return $this;
    }

    /**
     * @return Collection|Planning[]
     */
    public function getPlannings(): Collection
    {
        return $this->plannings;
    }

    public function addPlanning(Planning $planning): self
    {
        if (!$this->plannings->contains($planning)) {
            $this->plannings[] = $planning;
        }

        return $this;
    }

    public function removePlanning(Planning $planning): self
    {
        $this->plannings->removeElement($planning);

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
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        $this->events->removeElement($event);

        return $this;
    }

    /**
     * @return Collection|Contact[]
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts[] = $contact;
            $contact->setUserCreate($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): self
    {
        if ($this->contacts->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getUserCreate() === $this) {
                $contact->setUserCreate(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Team[]
     */
    public function getInvitedTeams(): Collection
    {
        return $this->invitedTeams;
    }

    public function addInvitedTeam(Team $invitedTeam): self
    {
        if (!$this->invitedTeams->contains($invitedTeam)) {
            $this->invitedTeams[] = $invitedTeam;
        }

        return $this;
    }

    public function removeInvitedTeam(Team $invitedTeam): self
    {
        $this->invitedTeams->removeElement($invitedTeam);

        return $this;
    }

    /**
     * @return Collection|Team[]
     */
    public function getOwnedTeams(): Collection
    {
        return $this->ownedTeams;
    }

    public function addOwnedTeam(Team $ownedTeam): self
    {
        if (!$this->ownedTeams->contains($ownedTeam)) {
            $this->ownedTeams[] = $ownedTeam;
            $ownedTeam->setUser($this);
        }

        return $this;
    }

    public function removeOwnedTeam(Team $ownedTeam): self
    {
        if ($this->ownedTeams->removeElement($ownedTeam)) {
            // set the owning side to null (unless already changed)
            if ($ownedTeam->getUser() === $this) {
                $ownedTeam->setUser(null);
            }
        }

        return $this;
    }


}
