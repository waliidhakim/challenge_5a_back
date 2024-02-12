<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Dto\PatchUserDto;
use App\Repository\FeedbackRepository;
use App\State\UserPasswordHasher;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FeedbackRepository::class)]
#[ApiResource(

    operations: [
        new Get(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage : "You don't have permission to perform this action",
        ),
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage : "You don't have permission to perform this action",
        ),
        new Patch (

        ),
        new Post(
            denormalizationContext : ['feedback:create']
        ),

        new Delete(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage : "You don't have permission to perform this action",
        )

    ],


)]
class Feedback
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['feedback:read', 'user:read'])]
    private ?string $comment = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['feedback:read', 'user:read'])]
    private ?int $rating = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['feedback:read', 'user:read'])]
    private ?\DateTimeInterface $feedbackDate = null;

    #[ORM\ManyToOne(inversedBy: 'feedbacks')]
    private ?User $issuedBy = null;

    #[ORM\ManyToOne(inversedBy: 'feedback')]
    private ?Prestation $prestation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getFeedbackDate(): ?\DateTimeInterface
    {
        return $this->feedbackDate;
    }

    public function setFeedbackDate(?\DateTimeInterface $feedbackDate): static
    {
        $this->feedbackDate = $feedbackDate;

        return $this;
    }

    public function getIssuedBy(): ?User
    {
        return $this->issuedBy;
    }

    public function setIssuedBy(?User $issuedBy): static
    {
        $this->issuedBy = $issuedBy;

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
}
