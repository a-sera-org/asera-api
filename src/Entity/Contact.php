<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Delete(),
    ],
    normalizationContext: ['groups' => ['user:read', 'default', 'company:read', 'job:read']],
    denormalizationContext: ['groups' => ['user:write', 'contact:write', 'default', 'company:write']],
    mercure: true
)]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false)]
#[UniqueEntity(
    fields: 'email',
    message: 'This email is already used.'
)]
#[Delete(security: "is_granted('IS_AUTHENTICATED_FULLY')")]
#[Put(security: "is_granted('IS_AUTHENTICATED_FULLY')")]
#[Patch(security: "is_granted('IS_AUTHENTICATED_FULLY')")]
class Contact
{
    use TimestampableEntity;
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['user:read', 'company:read', 'job:read', 'job:read'])]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:write', 'contact:write', 'company:write', 'job:read', 'company:read'])]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: true)]
    #[Groups(['user:read', 'user:write', 'contact:write', 'company:write', 'job:read', 'company:read'])]
    private ?array $phones = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['user:read', 'user:write', 'contact:write', 'job:read', 'company:read'])]
    private ?string $linkedin = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'user:write', 'contact:write'])]
    private ?string $github = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'user:write', 'contact:write'])]
    private ?string $stackoverflow = null;

    #[ORM\ManyToOne(inversedBy: 'contact')]
    private ?Company $company = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'user:write', 'contact:write', 'company:write', 'job:read', 'company:read'])]
    private ?string $web = null;

    /**
     * When this object is called to be a string, force to return email
     * Do not give an error.
     */
    public function __toString(): string
    {
        return $this->email;
    }

    public function getId(): ?string
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

    public function getPhones(): ?array
    {
        return $this->phones;
    }

    public function setPhones(?array $phones): static
    {
        $this->phones = $phones;

        return $this;
    }

    public function getLinkedin(): ?string
    {
        return $this->linkedin;
    }

    public function setLinkedin(?string $linkedin): static
    {
        $this->linkedin = $linkedin;

        return $this;
    }

    public function getGithub(): ?string
    {
        return $this->github;
    }

    public function setGithub(?string $github): static
    {
        $this->github = $github;

        return $this;
    }

    public function getStackoverflow(): ?string
    {
        return $this->stackoverflow;
    }

    public function setStackoverflow(?string $stackoverflow): static
    {
        $this->stackoverflow = $stackoverflow;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getWeb(): ?string
    {
        return $this->web;
    }

    public function setWeb(?string $web): static
    {
        $this->web = $web;

        return $this;
    }
}
