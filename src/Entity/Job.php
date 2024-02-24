<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\NumericFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
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
use App\Entity\Enum\JobCategory;
use App\Entity\Enum\JobType;
use App\Entity\Enum\WorkType;
use App\Repository\JobRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;



#[ORM\Entity(repositoryClass: JobRepository::class)]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false)]
#[ApiResource(
    operations: [
        new Delete(security: "is_granted('IS_AUTHENTICATED_FULLY')"),
        new Post(security: "is_granted('IS_AUTHENTICATED_FULLY')"),
        new Put(security: "is_granted('IS_AUTHENTICATED_FULLY')"),
        new Patch(security: "is_granted('IS_AUTHENTICATED_FULLY')"),
        new Get(),
        new GetCollection(),
    ],
    normalizationContext: ['groups' => ['job:read']],
    denormalizationContext: ['groups' => ['job:write']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'company.name' => 'partial',
    'title' => 'partial',
    'description' => 'partial',
])]
#[ApiFilter(NumericFilter::class, properties: ['workType', 'contract', 'jobCategory'])]
#[ApiFilter(RangeFilter::class, properties: ['salary'])]
class Job
{
    use TimestampableEntity;
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['job:read'])]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['job:read', 'job:write', 'job:application:read'])]
    #[ApiProperty(description: 'the user')]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['job:read', 'job:write', 'job:application:read'])]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['job:read', 'job:write', 'job:application:read'])]
    private ?string $salary = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['job:read', 'job:write', 'job:application:read'])]
    private ?string $diploma = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['job:read', 'job:write', 'job:application:read'])]
    private ?string $experiences = null;

    #[ORM\ManyToOne(inversedBy: 'jobs')]
    #[Groups(['job:read', 'job:write', 'job:application:read'])]
    private ?Company $company = null;

    #[ORM\Column]
    #[Groups(['job:read', 'job:write', 'job:application:read'])]
    private ?int $contract = 1;

    #[ORM\Column]
    #[Groups(['job:read', 'job:write', 'job:application:read'])]
    private ?int $workType = 1;

    #[ORM\Column]
    #[Groups(['job:read', 'job:write', 'job:application:read'])]
    private ?int $jobCategory = 1;

    #[ORM\Column(nullable: true)]
    private ?bool $isEnabled = true;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'created_by', referencedColumnName: 'id')]
    #[Gedmo\Blameable(on: 'create')]
    private ?User $createdBy;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'updated_by', referencedColumnName: 'id')]
    #[Gedmo\Blameable(on: 'update')]
    private ?User $updatedBy;

    #[ORM\OneToMany(mappedBy: 'job', targetEntity: JobApplication::class, orphanRemoval: true)]
    #[Groups('job:read')]
    private Collection $jobApplications;

    public function __construct()
    {
        $this->jobApplications = new ArrayCollection();
    }

    public function getId(): ?string
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getSalary(): ?string
    {
        return $this->salary;
    }

    public function setSalary(?string $salary): static
    {
        $this->salary = $salary;

        return $this;
    }

    public function getDiploma(): ?string
    {
        return $this->diploma;
    }

    public function setDiploma(?string $diploma): static
    {
        $this->diploma = $diploma;

        return $this;
    }

    public function getExperiences(): ?string
    {
        return $this->experiences;
    }

    public function setExperiences(?string $experiences): static
    {
        $this->experiences = $experiences;

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

    public function getContract(): ?int
    {
        return $this->contract;
    }

    public function setContract(int $contract): static
    {
        $this->contract = $contract;

        return $this;
    }

    public function getWorkType(): ?int
    {
        return $this->workType;
    }

    public function setWorkType(int $workType): static
    {
        $this->workType = $workType;

        return $this;
    }

    public function getJobCategory(): ?int
    {
        return $this->jobCategory;
    }

    public function setJobCategory(?int $jobCategory): static
    {
        $this->jobCategory = $jobCategory;

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

    /**
     * @return Collection<int, JobApplication>
     */
    public function getJobApplications(): Collection
    {
        return $this->jobApplications;
    }

    public function addJobApplication(JobApplication $jobApplication): static
    {
        if (!$this->jobApplications->contains($jobApplication)) {
            $this->jobApplications->add($jobApplication);
            $jobApplication->setJob($this);
        }

        return $this;
    }

    public function removeJobApplication(JobApplication $jobApplication): static
    {
        if ($this->jobApplications->removeElement($jobApplication)) {
            // set the owning side to null (unless already changed)
            if ($jobApplication->getJob() === $this) {
                $jobApplication->setJob(null);
            }
        }

        return $this;
    }
}
