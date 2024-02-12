<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\AddEstablishmentToPrestataireController;
use App\Controller\DeletePrestataireController;
use App\Controller\GetEstablishmentsByPrestataireController;
use App\Controller\RegisterPrestataireController;
use App\Dto\RegisterPrestataireDto;
use App\Repository\PrestataireRepository;
use App\State\ApprovePrestataireProcessor;
use App\State\GetEmployeesByPrestataireStateProvider;
use App\State\GetEstablishmentByPrestataireStateProvider;
use App\State\GetPrestatairesForUserStateProvider;
use App\State\RegisterPrestataireProcessor;
use App\State\RejectPrestataireProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PrestataireRepository::class)]
#[ApiResource(
   operations: [
        new Post(

//            input: RegisterPrestataireDto::class,
            security: "is_granted('ROLE_USER') or is_granted('ROLE_PRESTATAIRE')",
            securityMessage : "You have to be logged in to perform this action",
            controller: RegisterPrestataireController::class,
//            processor: RegisterPrestataireProcessor::class,
            deserialize: false,
            validationContext: ['groups' => ['Default', 'prestataire:create']],
            uriTemplate: '/prestataire/register',
            denormalizationContext :  ['groups' => ['prestataire:create']]
        ),


        new Post(

            security: "is_granted('ROLE_PRESTATAIRE')",
            securityMessage : "You don't have permission to perform this action",
            controller: AddEstablishmentToPrestataireController::class,
            deserialize: false,
//            validationContext: ['groups' => ['Default', 'prestataire:create']],
            uriTemplate: '/prestataire/{id}/addEstablishment',
            denormalizationContext :  ['groups' => ['prestataire:add:establishment']]
        ),

       new Get(
           security: "is_granted('ROLE_ADMIN') or ( is_granted('ROLE_PRESTATAIRE') and object.getOwner() == user)",
//           security: "is_granted('ROLE_PRESTATAIRE') and object.getOwner() == user",
           securityMessage : "You don't have permission to access this page",
           normalizationContext :  ['groups' => ['prestataire:read']]
       ),
       new GetCollection(
           security: "is_granted('ROLE_ADMIN') or ( is_granted('ROLE_PRESTATAIRE'))",
           securityMessage : "You don't have permission to perform this action",
           provider: GetPrestatairesForUserStateProvider::class ,
           normalizationContext :  ['groups' => ['prestataire:collection:read']]
       ),


        new GetCollection(
            security: "( is_granted('ROLE_PRESTATAIRE'))",
            securityMessage : "You don't have permission to perform this action",
            uriTemplate: '/prestataire/{id}/establishments',
            provider: GetEstablishmentByPrestataireStateProvider::class,
            normalizationContext :  ['groups' => ['prestataire:establishments:read']]
        ),

        new GetCollection(
            security: "( is_granted('ROLE_PRESTATAIRE'))",
            securityMessage : "You don't have permission to perform this action",
            uriTemplate: '/prestataire/employees',
            provider: GetEmployeesByPrestataireStateProvider::class,
            normalizationContext :  ['groups' => ['prestataire:employees:read']]
        ),

        new Patch(
           security: "is_granted('ROLE_ADMIN') or ( is_granted('ROLE_PRESTATAIRE') and object.getOwner() == user)",
           securityMessage : "You don't have permission to perform this action",
           validationContext: ['groups' => ['Default', 'prestataire:update']],
           denormalizationContext :  ['groups' => ['prestataire:update']]
       ),

        new Put(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage : "You don't have permission to perform this action",
            uriTemplate: '/prestataires/{id}/approve',
            processor: ApprovePrestataireProcessor::class,
            denormalizationContext :  ['groups' => ['prestataire:approuval']]
        ),
        new Put(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage : "You don't have permission to perform this action",
            uriTemplate: '/prestataires/{id}/reject',
            processor: RejectPrestataireProcessor::class,
            denormalizationContext :  ['groups' => ['prestataire:rejection']]
        ),
       new Delete(
//           security: "is_granted('ROLE_ADMIN')",
//           securityMessage : "You don't have permission to perform this action",
//            path: '/prestataires/{id}',
            controller: DeletePrestataireController::class,
            security: "is_granted('ROLE_ADMIN') or (is_granted('ROLE_PRESTATAIRE') and object.getOwner() == user)",
            securityMessage: "You don't have permission to perform this action",
       )
    ]
)]
class Prestataire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]


    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['prestataire:create',
        'prestataire:read',
        'prestataire:update',
        'prestataire:collection:read',
        'establishment:create',
        'establishment:read',
    ])]
    #[Assert\NotBlank(groups: ['prestataire:create'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'prestataire:create',
        'prestataire:collection:read',
        'prestataire:read',
        'prestataire:update'
    ])]
    #[Assert\NotBlank(groups: ['prestataire:create'])]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'prestataire:create',
        'prestataire:read',
        'prestataire:collection:read',
        'prestataire:update'
    ])]
    #[Assert\NotBlank(groups: ['prestataire:create'])]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'prestataire:create',
        'prestataire:collection:read',
        'prestataire:read',
        'prestataire:update'
    ])]
    #[Assert\NotBlank(groups: ['prestataire:create'])]
    private ?string $contactInfos = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'prestataire:create',
        'prestataire:collection:read',
        'prestataire:read',
        'prestataire:update'
    ])]
    #[Assert\NotBlank(groups: ['prestataire:create'])]
    private ?string $sector = null;

    #[ORM\OneToMany(mappedBy: 'relateTo', targetEntity: Establishment::class )]
    #[Groups([
        'prestataire:establishments:read',
        'prestataire:read',
        'prestataire:collection:read',
        'prestataire:add:establishment',
        'prestataire:employees:read'
    ])]
    private Collection $establishments;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['prestataire:create','prestataire:collection:read','prestataire:read','prestataire:update'])]
    #[Assert\NotBlank(groups: ['prestataire:create'])]
    private ?string $kbis = null;

    #[Groups(['prestataire:collection:read','prestataire:update'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'prestataires', cascade: ['remove'])]
    #[Groups(['prestataire:collection:read'])]
    private ?User $owner = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['prestataire:create','prestataire:collection:read','prestataire:read'])]
    private ?string $image = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Media $media = null;

    public function __construct()
    {
        $this->establishments = new ArrayCollection();
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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

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

    public function getContactInfos(): ?string
    {
        return $this->contactInfos;
    }

    public function setContactInfos(?string $contactInfos): static
    {
        $this->contactInfos = $contactInfos;

        return $this;
    }

    public function getSector(): ?string
    {
        return $this->sector;
    }

    public function setSector(?string $sector): static
    {
        $this->sector = $sector;

        return $this;
    }

    /**
     * @return Collection<int, Establishment>
     */
    public function getEstablishments(): Collection
    {
        return $this->establishments;
    }

    public function addEstablishment(Establishment $establishment): static
    {
        if (!$this->establishments->contains($establishment)) {
            $this->establishments->add($establishment);
            $establishment->setRelateTo($this);
        }

        return $this;
    }

    public function removeEstablishment(Establishment $establishment): static
    {
        if ($this->establishments->removeElement($establishment)) {
            // set the owning side to null (unless already changed)
            if ($establishment->getRelateTo() === $this) {
                $establishment->setRelateTo(null);
            }
        }

        return $this;
    }

    public function getKbis(): ?string
    {
        return $this->kbis;
    }

    public function setKbis(?string $kbis): static
    {
        $this->kbis = $kbis;

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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

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
}
