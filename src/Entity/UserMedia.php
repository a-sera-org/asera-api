<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Entity\Traits\TimestampableEntity;
use App\Repository\UserMediaRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserMediaRepository::class)]
#[ApiResource(
    operations: [
        new Delete(security: "is_granted('IS_AUTHENTICATED_FULLY')"),
        new Post(security: "is_granted('IS_AUTHENTICATED_FULLY')"),
        new Put(security: "is_granted('IS_AUTHENTICATED_FULLY')"),
        new Patch(security: "is_granted('IS_AUTHENTICATED_FULLY')"),
        new Get(),
        new GetCollection(),
    ],
    normalizationContext: ['groups' => ['user:media:read', 'user:read'], 'enable_max_depth' => true],
    denormalizationContext: ['groups' => ['user:media:write', 'user:write']],
    mercure: false
)]
#[ApiFilter(OrderFilter::class, properties: ['createdAt' => 'DESC'])]
class UserMedia
{
    use TimestampableEntity;
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['user:read'])]
    private ?Uuid $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(['user:media:read', 'user:media:write', 'user:read', 'user:write', 'job:read'])]
    private ?MediaObject $profilePicture = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(['user:media:read', 'user:media:write', 'user:read', 'user:write', 'job:read'])]
    private ?MediaObject $coverPicture = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(['user:media:read', 'user:media:write', 'user:read', 'user:write', 'job:read'])]
    private ?MediaObject $cv = null;

    #[ORM\OneToOne(mappedBy: 'media', cascade: ['persist', 'remove'])]
    #[Groups(['user:media:read', 'user:media:write'])]
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
        if (null === $owner && null !== $this->owner) {
            $this->owner->setMedia(null);
        }

        // set the owning side of the relation if necessary
        if (null !== $owner && $owner->getMedia() !== $this) {
            $owner->setMedia($this);
        }

        $this->owner = $owner;

        return $this;
    }
}
