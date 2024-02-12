<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\DeteteBookingController;
use App\Repository\BookingRepository;
use App\State\GetBookingsStateProvider;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
#[ApiResource(

    operations: [
        new Get(
            security: "is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object.getBookedBy() == user)",
            securityMessage : "You don't have permission to perform this action",
            normalizationContext: ['groups' => ['booking:read']]
        ),
        new GetCollection(
            security: "is_granted('ROLE_USER')",
            securityMessage : "You don't have permission to perform this action",
            provider: GetBookingsStateProvider::class,
            normalizationContext: ['groups' => ['booking:read']]
        ),
//        new Put(
//            security: "is_granted('ROLE_USER')",
//        ),
        new Patch (
            security: "is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object.getBookedBy() == user)",
            securityMessage : "You don't have permission to perform this action",
            denormalizationContext: ['groups' => ['booking:update']]
        ),
        new Post(
            denormalizationContext : ['booking:create']
        ),

        new Delete(
            security: "is_granted('ROLE_USER')",
            securityMessage : "You don't have permission to perform this action",
            controller: DeteteBookingController::class
        )

    ],


)]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['booking:read'])]

    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['booking:read', 'booking:update'])]
    private ?\DateTimeInterface $bookingDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[Groups(['booking:read'])]
    private ?User $bookedBy = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[Groups(['booking:read'])]
    private ?Prestation $prestation = null;

    #[ORM\ManyToOne(inversedBy: 'booking')]
    private ?Slot $slot = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBookingDate(): ?\DateTimeInterface
    {
        return $this->bookingDate;
    }

    public function setBookingDate(?\DateTimeInterface $bookingDate): static
    {
        $this->bookingDate = $bookingDate;

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

    public function getBookedBy(): ?User
    {
        return $this->bookedBy;
    }

    public function setBookedBy(?User $bookedBy): static
    {
        $this->bookedBy = $bookedBy;

        return $this;
    }

    public function getPrestation(): ?Prestation
    {
        return $this->prestation;
    }

    public function setPrestation(?Prestation $prestation): static
    {
        $this->prestation = $prestation;

        return $this;
    }

    public function getSlot(): ?Slot
    {
        return $this->slot;
    }

    public function setSlot(?Slot $slot): static
    {
        $this->slot = $slot;

        return $this;
    }
}
