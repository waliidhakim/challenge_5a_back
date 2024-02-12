<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\MediaOploaderController;
use App\Dto\PatchUserDto;
use App\Dto\RegisterEmployeeDto;
use App\Dto\RegisterUserDto;
use App\Repository\UserRepository;
use App\State\GetEmployeesStateProvider;
use App\State\RegisterEmployeeProcessor;
use App\State\UserPasswordHasher;
//use App\State\UserPrestataireStateProvider;
use App\State\UserPatchProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]


#[ApiResource(
    operations : [
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage : "You are not authorized to perform this action !",
            normalizationContext: ['groups' => ['user:read']]
        ),
        new GetCollection(
            security: "is_granted('ROLE_MANAGER')",
            securityMessage : "You are not authorized to perform this action !",
            uriTemplate: "/users/employees",
            provider: GetEmployeesStateProvider::class ,
            normalizationContext: ['groups' => ['user:read']]
        ),
        new Get(
            security: "( is_granted('ROLE_USER') and object.getEmail() == user.getEmail())",
            securityMessage : "You are not authorized to perform this action !",
            normalizationContext: ['groups' => ['user:read']]

        ),
        new Post(
            processor: UserPasswordHasher::class,
//            input: RegisterUserDto::class,
            validationContext: ['groups' => ['Default', 'user:create']],
            name: 'registration',
            uriTemplate: '/register',
            denormalizationContext :  ['groups' => ['user:create']]
        ),

        new Post(
            //////////////////////////////////
            processor: RegisterEmployeeProcessor::class ,
            input: RegisterEmployeeDto::class ,
            security: "is_granted('ROLE_MANAGER')",
            securityMessage : "You are not authorized to perform this action !",
            validationContext: ['groups' => ['Default', 'user:create']],
            uriTemplate: '/registerEmployee',
            normalizationContext: ['groups' => ['user:read']]
        ),
        new Patch(
            security: "is_granted('ROLE_ADMIN') or ( is_granted('ROLE_USER') and object.getEmail() == user.getEmail())",
            securityMessage : "You are not authorized to perform this action !",
            denormalizationContext :  ['groups' => ['user:update']]
        ),
        new Post(
            security: "is_granted('ROLE_ADMIN') or ( is_granted('ROLE_USER') and object.getEmail() == user.getEmail())",
            securityMessage : "You are not authorized to perform this action !",
            controller: MediaOploaderController::class,
            deserialize: false,
            uriTemplate: '/update_profil/{id}',
            denormalizationContext :  ['groups' => ['user:update']]
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage : "You are not authorized to perform this action !",
        )
    ]

)]

#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
//    #[Groups(['user:read'])]
    #[Groups(['prestataire:approuval', 'prestataire:read','prestataire:employees:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['user:read','user:create','establishment:read','prestataire:employees:read'])]
    #[Assert\NotBlank(groups: ['user:create'])]
    #[Assert\Email(groups: ['user:create'], message:"Invalid email adress")]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['user:read','prestataire:employees:read'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(['user:create'])]
    #[Assert\NotBlank(groups: ['user:create'])]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:create'])]
    #[Assert\NotBlank(groups: ['user:create'])]
    private ?string $confirmPassword = null;


    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'user:read',
        'user:create', 'user:update',
        'establishment:read',
        'prestataire:collection:read',
        'prestataire:read',
        'prestataire:employees:read',
        'booking:read'
    ])]
    #[Assert\NotBlank(groups: ['user:create','prestataire:collection:read'])]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'user:create',
        'user:update',
        'user:read',
        'prestataire:collection:read',
        'establishment:read',
        'prestataire:read',
        'prestataire:employees:read',
        'booking:read'
    ])]
    #[Assert\NotBlank(groups: ['user:create'])]
    private ?string $lastname = null;


    #[ORM\OneToMany(mappedBy: 'issuedBy', targetEntity: Feedback::class)]

//    #[Groups(['user:read'])]
    private Collection $feedbacks;

    #[ORM\OneToMany(mappedBy: 'bookedBy', targetEntity: Booking::class)]
    private Collection $bookings;

    #[ORM\ManyToOne(inversedBy: 'employees',cascade: ["remove"])]
    private ?Establishment $establishment = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Prestataire::class)]
    private Collection $prestataires;

    #[ORM\OneToMany(mappedBy: 'manager', targetEntity: Establishment::class)]
    private Collection $managedEstablishments;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'prestataire:employees:read',
        'establishment:read',
    ])]
    private ?string $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'user:read',
        'user:update',
        'prestataire:employees:read',
        'establishment:read',
    ])]
    private ?string $image = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Media $media = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'user:create',
        'user:update',
        'user:read',
        'prestataire:collection:read',
        'establishment:read',
        'prestataire:read',
        'prestataire:employees:read'
    ])]
    private ?string $address = null;

    #[ORM\OneToMany(mappedBy: 'employee', targetEntity: EmployeeSchedule::class)]
    private Collection $employeeSchedules;

    public function __construct()
    {
        $this->roles = ['ROLE_USER'];
        $this->feedbacks = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->prestataires = new ArrayCollection();
        $this->managedEstablishments = new ArrayCollection();
        $this->employeeSchedules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
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
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
//        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
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

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }


    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(?string $confirmPassword): static
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }

    /**
     * @return Collection<int, Feedback>
     */
    public function getFeedbacks(): Collection
    {
        return $this->feedbacks;
    }

    public function addFeedback(Feedback $feedback): static
    {
        if (!$this->feedbacks->contains($feedback)) {
            $this->feedbacks->add($feedback);
            $feedback->setIssuedBy($this);
        }

        return $this;
    }

    public function removeFeedback(Feedback $feedback): static
    {
        if ($this->feedbacks->removeElement($feedback)) {
            // set the owning side to null (unless already changed)
            if ($feedback->getIssuedBy() === $this) {
                $feedback->setIssuedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->setBookedBy($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getBookedBy() === $this) {
                $booking->setBookedBy(null);
            }
        }

        return $this;
    }

    public function getEstablishment(): ?Establishment
    {
        return $this->establishment;
    }

    public function setEstablishment(?Establishment $establishment): static
    {
        $this->establishment = $establishment;

        return $this;
    }

    /**
     * @return Collection<int, Prestataire>
     */
    public function getPrestataires(): Collection
    {
        return $this->prestataires;
    }

    public function addPrestataire(Prestataire $prestataire): static
    {
        if (!$this->prestataires->contains($prestataire)) {
            $this->prestataires->add($prestataire);
            $prestataire->setOwner($this);
        }

        return $this;
    }

    public function removePrestataire(Prestataire $prestataire): static
    {
        if ($this->prestataires->removeElement($prestataire)) {
            // set the owning side to null (unless already changed)
            if ($prestataire->getOwner() === $this) {
                $prestataire->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Establishment>
     */
    public function getManagedEstablishments(): Collection
    {
        return $this->managedEstablishments;
    }

    public function addManagedEstablishment(Establishment $managedEstablishment): static
    {
        if (!$this->managedEstablishments->contains($managedEstablishment)) {
            $this->managedEstablishments->add($managedEstablishment);
            $managedEstablishment->setManager($this);
        }

        return $this;
    }

    public function removeManagedEstablishment(Establishment $managedEstablishment): static
    {
        if ($this->managedEstablishments->removeElement($managedEstablishment)) {
            // set the owning side to null (unless already changed)
            if ($managedEstablishment->getManager() === $this) {
                $managedEstablishment->setManager(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): static
    {
        $this->media = $media;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection<int, EmployeeSchedule>
     */
    public function getEmployeeSchedules(): Collection
    {
        return $this->employeeSchedules;
    }

    public function addEmployeeSchedule(EmployeeSchedule $employeeSchedule): static
    {
        if (!$this->employeeSchedules->contains($employeeSchedule)) {
            $this->employeeSchedules->add($employeeSchedule);
            $employeeSchedule->setEmployee($this);
        }

        return $this;
    }

    public function removeEmployeeSchedule(EmployeeSchedule $employeeSchedule): static
    {
        if ($this->employeeSchedules->removeElement($employeeSchedule)) {
            // set the owning side to null (unless already changed)
            if ($employeeSchedule->getEmployee() === $this) {
                $employeeSchedule->setEmployee(null);
            }
        }

        return $this;
    }
}
