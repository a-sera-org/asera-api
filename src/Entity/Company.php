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
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\BackOffice\CompaniesController;
use App\Entity\Traits\TimestampableEntity;
use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[ApiResource(
    operations: [
        new Delete(security: "is_granted('IS_AUTHENTICATED_FULLY')"),
        new Put(security: "is_granted('IS_AUTHENTICATED_FULLY')"),
        new Patch(security: "is_granted('IS_AUTHENTICATED_FULLY')"),
        new Post(security: "is_granted('IS_AUTHENTICATED_FULLY')"),
        new Post(security: "is_granted('IS_AUTHENTICATED_FULLY')", uriTemplate: "/companies/{id}/add-collaborator"),
        new Delete(security: "is_granted('IS_AUTHENTICATED_FULLY')", uriTemplate: "/companies/{id}/remove-collaborator/{collaboratorId}"),
        new Get(),
        new GetCollection(),
    ],
    normalizationContext: ['groups' => ['company:read', 'job:read', 'user:read']],
    denormalizationContext: ['groups' => ['company:write', 'recruiter:write'], 'enable_max_depth' => true],
    mercure: false
)]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false)]
#[UniqueEntity(fields: 'name', message: 'Name already in use')]
#[ApiFilter(SearchFilter::class, properties: [
    'name' => 'partial',
    'address.city' => 'partial',
    'address.country' => 'partial',
    'nif' => 'exact',
    'stat' => 'exact',
    'description' => 'partial',
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt' => 'DESC'])]
class Company
{
    use SoftDeleteableEntity;
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['company:read', 'job:read'])]
    private ?Uuid $id = null;

    #[ORM\Column(length: 200)]
    #[Groups(['company:write', 'company:read', 'job:read', 'recruiter:write'])]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Contact::class, cascade: ['persist', 'remove'])]
    #[Groups(['company:write', 'company:read', 'job:read', 'recruiter:write'])]
    #[Assert\Valid]
    private Collection $contact;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['company:write', 'company:read', 'recruiter:write'])]
    private ?string $nif = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['company:write', 'company:read', 'recruiter:write'])]
    private ?string $stat = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['company:write', 'company:read', 'job:read', 'recruiter:write'])]
    private ?int $type = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ApiProperty(types: ['https://schema.org/image'])]
    #[Groups(['company:write', 'company:read', 'job:read', 'recruiter:write'])]
    private ?MediaObject $logo = null;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Job::class)]
    private Collection $jobs;

    #[ORM\Column]
    #[Groups(['company:write', 'company:read'])]
    private ?bool $isEnabled = true;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: User::class)]
    #[Groups(['company:write', 'company:read', 'recruiter:write'])]
    private Collection $collaborators;

    #[ORM\OneToMany(mappedBy: 'ownCompany', targetEntity: User::class)]
    #[Groups(['company:write', 'company:read', 'recruiter:write'])]
    private Collection $admins;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'created_by', referencedColumnName: 'id')]
    #[Gedmo\Blameable(on: 'create')]
    #[Groups(['company:read'])]
    private ?User $owner;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'updated_by', referencedColumnName: 'id')]
    #[Gedmo\Blameable(on: 'update')]
    #[Groups(['company:read'])]
    private ?User $updatedBy;

    #[ORM\Column(nullable: true)]
    #[Groups(['company:read', 'job:read'])]
    private ?bool $isVerified = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(['company:write', 'company:read'])]
    private ?Addresse $address = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['company:write', 'company:read'])]
    private ?string $description = null;

    public function __construct()
    {
        $this->contact = new ArrayCollection();
        $this->jobs = new ArrayCollection();
        $this->collaborators = new ArrayCollection();
        $this->admins = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Contact>
     */
    public function getContact(): Collection
    {
        return $this->contact;
    }

    public function addContact(Contact $contact): static
    {
        if (!$this->contact->contains($contact)) {
            $this->contact->add($contact);
            $contact->setCompany($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): static
    {
        if ($this->contact->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getCompany() === $this) {
                $contact->setCompany(null);
            }
        }

        return $this;
    }

    public function getNif(): ?string
    {
        return $this->nif;
    }

    public function setNif(?string $nif): static
    {
        $this->nif = $nif;

        return $this;
    }

    public function getStat(): ?string
    {
        return $this->stat;
    }

    public function setStat(?string $stat): static
    {
        $this->stat = $stat;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getLogo(): ?MediaObject
    {
        return $this->logo;
    }

    public function setLogo(?MediaObject $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * @return Collection<int, Job>
     */
    public function getJobs(): Collection
    {
        return $this->jobs;
    }

    public function addJob(Job $job): static
    {
        if (!$this->jobs->contains($job)) {
            $this->jobs->add($job);
            $job->setCompany($this);
        }

        return $this;
    }

    public function removeJob(Job $job): static
    {
        if ($this->jobs->removeElement($job)) {
            // set the owning side to null (unless already changed)
            if ($job->getCompany() === $this) {
                $job->setCompany(null);
            }
        }

        return $this;
    }

    public function isIsEnabled(): ?bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): static
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getCollaborators(): Collection
    {
        return $this->collaborators;
    }

    public function addCollaborator(User $collaborator): static
    {
        if (!$this->collaborators->contains($collaborator)) {
            $this->collaborators->add($collaborator);
            $collaborator->setCompany($this);
        }

        return $this;
    }

    public function removeCollaborator(User $collaborator): static
    {
        if ($this->collaborators->removeElement($collaborator)) {
            // set the owning side to null (unless already changed)
            if ($collaborator->getCompany() === $this) {
                $collaborator->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getAdmins(): Collection
    {
        return $this->admins;
    }

    public function addAdmin(User $admin): static
    {
        if (!$this->admins->contains($admin)) {
            $this->admins->add($admin);
            $admin->setOwnCompany($this);
        }

        return $this;
    }

    public function removeAdmin(User $admin): static
    {
        if ($this->admins->removeElement($admin)) {
            // set the owning side to null (unless already changed)
            if ($admin->getOwnCompany() === $this) {
                $admin->setOwnCompany(null);
            }
        }

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): Company
    {
        $this->owner = $owner;

        return $this;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?User $updatedBy): Company
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function isIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(?bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getAddress(): ?Addresse
    {
        return $this->address;
    }

    public function setAddress(?Addresse $address): static
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
}
