<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\Api\ApiRecruiterController;
use App\Controller\Api\ApiUserCollaboratorController;
use App\Controller\Api\ApiUserController;
use App\Entity\Traits\TimestampableEntity;
use App\State\UserPasswordHasher;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\PasswordStrength;

#[ORM\Entity]
#[ApiResource(
    operations: [
        new GetCollection(security: "is_granted('ROLE_ADMIN')"),
        new Post(
            uriTemplate: '/register/user',
            controller: ApiUserController::class,
            security: null,
            name: 'register_user',
            processor: UserPasswordHasher::class
        ),
        new Post(
            uriTemplate: '/register/recruiter',
            controller: ApiRecruiterController::class,
            denormalizationContext: ['groups' => ['recruiter:write']],
            security: null,
            name: 'register_recruiter',
            processor: UserPasswordHasher::class
        ),
        new Post(
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            uriTemplate: "/add-collaborator/{companyId}",
            uriVariables: [
                "companyId" => new Link(fromClass: Company::class, toProperty: 'company')
            ],
            controller: ApiUserCollaboratorController::class,
            processor: UserPasswordHasher::class
        ),
        new Get(security: "is_granted('ROLE_ADMIN') or object.getOwner() == user"),
        new Put(
            security: "is_granted('ROLE_ADMIN') or object.getOwner() == user",
            processor: UserPasswordHasher::class
        ),
        new Patch(
            security: "is_granted('ROLE_ADMIN') or object.getOwner() == user",
            processor: UserPasswordHasher::class
        ),
        new Delete(security: "is_granted('ROLE_ADMIN') or object.getOwner() == user"),
    ],
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']],
    mercure: false,
)]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: 'username', message: 'There is already an account with this username')]
#[ApiFilter(SearchFilter::class, properties: [
    'username' => 'partial',
    'firstname' => 'partial',
    'lastname' => 'partial',
    'contact.email' => 'partial',
    'contact.phones' => 'partial',
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt' => 'DESC'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableEntity;
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['user:read'])]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'user:write', 'recruiter:write', 'job:application:read', 'job:read'])]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:write', 'recruiter:write', 'job:application:read', 'job:read'])]
    private ?string $lastname = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(['user:read', 'user:write', 'recruiter:write', 'job:application:read', 'job:read'])]
    #[Assert\Valid]
    private ?Contact $contact = null;

    #[ORM\Column(length: 100, unique: true)]
    #[Assert\Length(min: 5, max: 20)]
    #[Groups(['user:read', 'user:write', 'recruiter:write', 'job:application:read', 'job:read'])]
    private ?string $username = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $password = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read', 'user:write', 'recruiter:write', 'job:application:read', 'job:read'])]
    private ?int $sex = null;

    #[ORM\Column(type: Types::JSON)]
    #[Groups(['user:read'])]
    private array $roles = ['ROLE_USER'];

    private ?string $salt = null;

    #[PasswordStrength([
        'minScore' => PasswordStrength::STRENGTH_WEAK,
        'message' => 'Your password is too easy to guess. Asera\'s security policy requires to use a stronger password.',
    ])]
    #[Groups(['user:write', 'recruiter:write'])]
    private ?string $plainPassword = null;

    #[ORM\OneToOne(inversedBy: 'owner', cascade: ['persist', 'remove'])]
    #[Groups(['user:read', 'user:write', 'recruiter:write', 'job:read'])]
    private ?UserMedia $media = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?bool $isEnabled = true;

    #[ApiProperty]
    #[ORM\ManyToOne(inversedBy: 'collaborators')]
    private ?Company $company = null;

    #[ORM\ManyToOne(inversedBy: 'admins')]
    private ?Company $ownCompany = null;

    public function getId(): ?string
    {
        return $this->id;
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

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): static
    {
        $this->contact = $contact;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getSex(): ?int
    {
        return $this->sex;
    }

    public function setSex(int $sex): static
    {
        $this->sex = $sex;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getUserIdentifier(): string
    {
        return $this->username ?? '';
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getOwner(): User
    {
        return $this;
    }

    public function getMedia(): ?UserMedia
    {
        return $this->media;
    }

    public function setMedia(?UserMedia $media): static
    {
        $this->media = $media;

        return $this;
    }

    public function isIsEnabled(): ?bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(?bool $isEnabled): static
    {
        $this->isEnabled = $isEnabled;

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

    public function getOwnCompany(): ?Company
    {
        return $this->ownCompany;
    }

    public function setOwnCompany(?Company $ownCompany): static
    {
        $this->ownCompany = $ownCompany;

        return $this;
    }
}
