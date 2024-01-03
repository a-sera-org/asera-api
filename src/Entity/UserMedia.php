<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Controller\IgnoredController;
use App\Repository\UserMediaRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserMediaRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(controller: IgnoredController::class)
    ],
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']],
    mercure: true
)]
class UserMedia
{
    use TimestampableEntity;
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['user:read', 'user:write'])]
    private ?Uuid $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(['user:read', 'user:write'])]
    private ?MediaObject $profilePicture = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(['user:read', 'user:write'])]
    private ?MediaObject $coverPicture = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(['user:read', 'user:write'])]
    private ?MediaObject $cv = null;

    #[ORM\OneToOne(mappedBy: 'media', cascade: ['persist', 'remove'])]
    private ?User $owner = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getProfilePicture(): ?MediaObject
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?MediaObject $profilePicture): static
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    public function getCoverPicture(): ?MediaObject
    {
        return $this->coverPicture;
    }

    public function setCoverPicture(?MediaObject $coverPicture): static
    {
        $this->coverPicture = $coverPicture;

        return $this;
    }

    public function getCv(): ?MediaObject
    {
        return $this->cv;
    }

    public function setCv(?MediaObject $cv): static
    {
        $this->cv = $cv;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        // unset the owning side of the relation if necessary
        if ($owner === null && $this->owner !== null) {
            $this->owner->setMedia(null);
        }

        // set the owning side of the relation if necessary
        if ($owner !== null && $owner->getMedia() !== $this) {
            $owner->setMedia($this);
        }

        $this->owner = $owner;

        return $this;
    }
}
