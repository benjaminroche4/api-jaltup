<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Enum\ContractType;
use App\Enum\PublicationStatus;
use App\Enum\StudyLevel;
use App\Repository\OfferRepository;
use App\Service\IdGeneratorService;
use Composer\Package\Link;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * The offer entity.
 */
#[ORM\Entity(repositoryClass: OfferRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    description: 'Get the offer information.',
    operations: [
        new Get(
            uriTemplate: '/offers/{publicId}',
            uriVariables: ['publicId'],
        ),
        new GetCollection(),
        new Post(),
    ],
    normalizationContext: [
        'groups' => ['offer:read'],
    ],
    denormalizationContext: [
        'groups' => ['offer:write'],
    ],
    paginationItemsPerPage: 40,
)]
class Offer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 20, unique: true)]
    #[Groups(['offer:read'])]
    #[Assert\Unique]
    #[ApiProperty(description: 'The public identifier of the offer.',)]
    private ?string $publicId = null;

    #[ORM\Column(length: 120)]
    #[ApiFilter(SearchFilter::class, strategy: 'partial')]
    #[Groups(['offer:read', 'offer:write', 'category:read', 'company:read'])]
    private ?string $title = null;

    #[ORM\Column(type: 'json')]
    #[Groups(['offer:read', 'offer:write'])]
    #[Assert\Collection(
        fields: [
            'fullAddress' => new Assert\Optional([
                new Assert\Type(type: 'string'),
            ]),
            'city' => [
                new Assert\Type(type: 'string'),
            ],
            'zipCode' => [
                new Assert\Type(type: 'integer'),
            ],
            'latitude' => new Assert\Optional([
                new Assert\Type(type: 'float'),
            ]),
            'longitude' => new Assert\Optional([
                new Assert\Type(type: 'float'),
            ]),
        ]
    )]
    #[ApiFilter(SearchFilter::class, strategy: 'partial')]
    private array $place = [
        'fullAddress' => '1 rue de Champs de Mars',
        'city' => 'Paris',
        'zipCode' => 75000,
        'latitude' => 48.866667,
        'longitude' => 2.333333,
    ];

    #[ORM\Column(type: 'json')]
    #[Groups(['offer:read', 'offer:write'])]
    #[Assert\Collection(
        fields: [
            'description' => [
                new Assert\Type(type: 'string'),
            ],
            'contractType' => new Assert\Optional([
                new Assert\Type(type: 'string'),
                new Assert\Choice(choices: [
                    ContractType::Apprenticeship->value,
                    ContractType::Permanent->value,
                    ContractType::Professionalization->value,
                    ContractType::FixedTerm->value,
                ], message: 'Invalid contract type'),
            ]),
            'duration' => new Assert\Optional([
                new Assert\Type(type: 'integer'),
            ]),
            'remote' => new Assert\Optional([
                new Assert\Type(type: 'boolean'),
            ]),
            'studyLevel' => new Assert\Optional([
                new Assert\Type(type: 'string'),
                new Assert\Choice(choices: [
                    StudyLevel::NoDiploma->value,
                    StudyLevel::BEP->value,
                    StudyLevel::CAP->value,
                    StudyLevel::BAC->value,
                    StudyLevel::BAC2->value,
                    StudyLevel::BAC3->value,
                    StudyLevel::BAC4->value,
                    StudyLevel::BAC5->value,
                ], message: 'Invalid study level'),
            ]),
            'startDate' => new Assert\Optional([
                new Assert\Type(type: 'string'),
            ]),
        ]
    )]
    private array $job = [
        'description' => 'This is the description...',
        'contractTye' => 'permanent',
        'duration' => 6,
        'remote' => false,
        'studyLevel' => 'bac+2',
        'startDate' => '2024-09-05T09:06:10.188Z',
    ];

    #[ORM\ManyToOne(inversedBy: 'offers')]
    #[Groups(['offer:read', 'offer:write', 'category:read'])]
    private ?Company $company = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Url]
    #[Groups(['offer:read', 'offer:write'])]
    #[ApiProperty(description: 'Original URL of the offer',)]
    private ?string $url = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['offer:read', 'offer:write'])]
    private ?array $tag = null;

    #[ORM\Column(length: 40)]
    #[Groups(['offer:read', 'offer:write'])]
    #[Assert\Choice(choices: [
        PublicationStatus::Published->value,
        PublicationStatus::Draft->value,
        PublicationStatus::Archived->value,
    ], message: 'Invalid status')]
    private ?string $status = null;

    #[ORM\Column]
    #[ApiFilter(OrderFilter::class)]
    #[Groups(['offer:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['offer:read'])]
    #[ApiProperty(description: 'The date when it will be set to "Archived".',)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(length: 255)]
    #[Groups(['offer:read'])]
    private ?string $slug = null;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'offers')]
    #[Groups(['offer:read', 'offer:write'])]
    private Collection $category;

    public function __construct()
    {
        $this->category = new ArrayCollection();
    }

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

    public function getPlace(): array
    {
        return $this->place;
    }

    public function setPlace(array $place): static
    {
        $this->place = $place;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getJob(): array
    {
        return $this->job;
    }

    public function setJob(array $job): static
    {
        $this->job = $job;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getTag(): ?array
    {
        return $this->tag;
    }

    public function setTag(?array $tag): static
    {
        $this->tag = $tag;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->category->contains($category)) {
            $this->category->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->category->removeElement($category);

        return $this;
    }

    public function getPublicId(): ?string
    {
        return $this->publicId;
    }

    public function setPublicId(string $publicId): static
    {
        $this->publicId = $publicId;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }
}