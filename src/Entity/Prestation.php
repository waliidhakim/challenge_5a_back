<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\ResearchPrestationController;
use App\Repository\PrestationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PrestationRepository::class)]
#[ApiResource(

    operations: [
        new Get(
            security: "is_granted('ROLE_USER')",
            securityMessage : "You don't have permission to perform this action",
            normalizationContext :  ['groups' => ['prestation:read']]
        ),
        new GetCollection(
//            security: "is_granted('ROLE_ADMIN')",
//            securityMessage : "You don't have permission to perform this action",
            normalizationContext :  ['groups' => ['prestation:collection:read']]
        ),

        new GetCollection(
            security: "is_granted('ROLE_PUBLIC_ACCESS')",
            securityMessage : "You don't have permission to perform this action",
            uriTemplate: '/prestations/public',
            normalizationContext :  ['groups' => ['prestation:collection:read:public']]
        ),

        new Post(
            uriTemplate: '/prestations/research',
            controller: ResearchPrestationController::class,
            read: false,
            output: false,
            denormalizationContext: ['groups' => ['prestation:search']],
            normalizationContext: ['groups' => ['prestation:collection:read']],
//            security: "is_granted('ROLE_PUBLIC_ACCESS')",
            openapiContext: [
                'summary' => 'Recherche des prestations par critères.',
                'description' => 'Recherche des prestations par nom, catégorie et prix.',
            ]
        ),

        new Post(
            denormalizationContext : ['prestation:create']
        ),

        new Delete(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage : "You don't have permission to perform this action",
        )

    ],


)]
class Prestation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['prestation:collection:read','prestation:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'prestation:collection:read',
        'prestation:read',
        'prestation:collection:read:public',
        'establishment:read',
        'prestation:search',
        'booking:read'
    ])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'prestation:collection:read',
        'prestation:read',
        'prestation:collection:read:public',
        'establishment:read',
    ])]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Groups([
        'prestation:collection:read',
        'prestation:read',
        'prestation:collection:read:public',
        'establishment:read'
    ])]
    private ?int $duration = null;

    #[ORM\Column(nullable: true)]
    #[Groups([
        'prestation:collection:read',
        'prestation:read',
        'prestation:collection:read:public',
        'establishment:read',
        'prestation:search',
        'booking:read'
    ])]
    private ?float $price = null;

    #[ORM\ManyToOne(inversedBy: 'prestations')]
    #[Groups(['prestation:read'])]
    private ?Establishment $establishment = null;

    #[ORM\ManyToOne(inversedBy: 'prestations')]
    #[Groups([
        'prestation:collection:read',
        'prestation:collection:read:public',
        'establishment:read',
        'prestation:search'
    ])]
    private ?Category $category = null;

    #[ORM\OneToMany(mappedBy: 'prestation', targetEntity: Booking::class)]
    private Collection $bookings;

    #[ORM\OneToMany(mappedBy: 'prestation', targetEntity: Slot::class)]
    private Collection $slots;

    #[ORM\OneToMany(mappedBy: 'prestation', targetEntity: Feedback::class)]
    private Collection $feedback;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Media $media = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'prestation:collection:read',
        'prestation:read',
        'prestation:collection:read:public',
        'establishment:read',
        'prestation:search',
        'booking:read'
    ])]
    private ?string $image = null;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
        $this->slots = new ArrayCollection();
        $this->feedback = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): static
    {
        $this->price = $price;

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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

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
            $booking->setPrestation($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getPrestation() === $this) {
                $booking->setPrestation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Slot>
     */
    public function getSlots(): Collection
    {
        return $this->slots;
    }

    public function addSlot(Slot $slot): static
    {
        if (!$this->slots->contains($slot)) {
            $this->slots->add($slot);
            $slot->setPrestation($this);
        }

        return $this;
    }

    public function removeSlot(Slot $slot): static
    {
        if ($this->slots->removeElement($slot)) {
            // set the owning side to null (unless already changed)
            if ($slot->getPrestation() === $this) {
                $slot->setPrestation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Feedback>
     */
    public function getFeedback(): Collection
    {
        return $this->feedback;
    }

    public function addFeedback(Feedback $feedback): static
    {
        if (!$this->feedback->contains($feedback)) {
            $this->feedback->add($feedback);
            $feedback->setPrestation($this);
        }

        return $this;
    }

    public function removeFeedback(Feedback $feedback): static
    {
        if ($this->feedback->removeElement($feedback)) {
            // set the owning side to null (unless already changed)
            if ($feedback->getPrestation() === $this) {
                $feedback->setPrestation(null);
            }
        }

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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }
}
