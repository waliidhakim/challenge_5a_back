<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\VideoOploaderController;
use App\Repository\VideoRepository;
use App\State\GetEmployeesStateProvider;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: VideoRepository::class)]
//#[ApiResource(
//   operations: [
//       new Post(
//            controller: VideoOploaderController::class,
//            deserialize: false
//       ),
//       new GetCollection(
//            normalizationContext: ['groups' => ['video:read']]
//       ),
//    ]
//)]
#[Vich\Uploadable]
class Video
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups('video:read')]
    private ?string $title = null;

    #[ORM\Column(nullable: true)]
    #[Groups('video:read')]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('video:read')]
    private ?string $name = null;

    #[Vich\UploadableField(mapping: 'video_uploads', fileNameProperty: 'name')]
    public ?File $file = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
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

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): void
    {
        $this->file = $file;
        if(null != $file){
            $this->updatedAt = new \DateTimeImmutable();
        }
    }
}
