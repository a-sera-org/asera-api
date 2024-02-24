<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\Traits\TimestampableEntity;
use App\Repository\JobApplicationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: JobApplicationRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['job:application:read']],
    denormalizationContext: ['groups' => ['job:application:write']],
    security: "is_granted('IS_AUTHENTICATED_FULLY')"
)]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false)]
#[ApiFilter(SearchFilter::class, properties: ['job' => 'exact'])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt' => 'DESC'])]
class JobApplication
{
    use TimestampableEntity;
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['job:application:read'])]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'jobApplications')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['job:application:read', 'job:application:write'])]
    private ?Job $job = null;

    #[ORM\ManyToOne]
    #[Groups(['job:read', 'job:application:read', 'job:application:write'])]
    private ?MediaObject $cv = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['job:read', 'job:application:read', 'job:application:write'])]
    private ?string $motivation = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'created_by', referencedColumnName: 'id')]
    #[Gedmo\Blameable(on: 'create')]
    #[Groups(['job:application:read'])]
    private ?User $candidat = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'updated_by', referencedColumnName: 'id')]
    #[Gedmo\Blameable(on: 'update')]
    #[Groups(['job:application:read'])]
    private ?User $updatedBy;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCandidat(): ?User
    {
        return $this->candidat;
    }

    public function setCandidat(?User $candidat): static
    {
        $this->candidat = $candidat;

        return $this;
    }

    public function getJob(): ?Job
    {
        return $this->job;
    }

    public function setJob(?Job $job): static
    {
        $this->job = $job;

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

    public function getMotivation(): ?string
    {
        return $this->motivation;
    }

    public function setMotivation(?string $motivation): static
    {
        $this->motivation = $motivation;

        return $this;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?User $updatedBy): JobApplication
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }
}
