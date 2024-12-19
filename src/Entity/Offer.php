<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
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
use App\State\OfferCountProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
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
            uriTemplate: '/offers/count',
            openapiContext: [
                'summary' => 'Get the number of published offers.',
                'responses' => [
                    '200' => [
                        'description' => 'The number of published offers.',
                        'content' => [
                            'application/ld+json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'count' => [
                                            'type' => 'integer',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            provider: OfferCountProvider::class,
        ),
        new Get(
            uriTemplate: '/offers/{publicId}',
            uriVariables: ['publicId'],
        ),
        new GetCollection(),
        new Post(
            openapiContext: [
                'security' => [
                    ['access_token' => []],
                ],
            ],
            security: 'is_granted("ROLE_ADMIN")',
            securityMessage: 'You don\'t have permission to access this resource.',
        ),
    ],
    normalizationContext: [
        'groups' => ['offer:read'],
    ],
    denormalizationContext: [
        'groups' => ['offer:write'],
    ],
    paginationClientItemsPerPage: true,
    paginationMaximumItemsPerPage: 100,
)]
class Offer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20, unique: true)]
    #[Groups(['offer:read'])]
    #[Assert\Unique]
    #[ApiProperty(description: 'The public identifier of the offer.', )]
    private ?string $publicId = null;

    #[ORM\Column(length: 120)]
    #[ApiFilter(SearchFilter::class, strategy: 'partial')]
    #[Groups(['offer:read', 'offer:write', 'category:read', 'company:read'])]
    #[Assert\Length(
        min: 5,
        max: 70,
        minMessage: 'The offer title must be at least {{ limit }} characters long',
        maxMessage: 'The offer title code be longer than {{ limit }} characters',
    )]
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
    private array $place = [];

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
                    StudyLevel::BAC8->value,
                ], message: 'Invalid study level'),
            ]),
            'startDate' => new Assert\Optional([
                new Assert\Type(type: 'string'),
            ]),
        ]
    )]
    private array $job = [];

    #[ORM\ManyToOne(inversedBy: 'offers')]
    #[Groups(['offer:read', 'offer:write', 'category:read'])]
    #[Assert\Length(
        min: 5,
        max: 120,
        minMessage: 'The offer company be at least {{ limit }} characters long',
        maxMessage: 'The offer company code be longer than {{ limit }} characters',
    )]
    #[ApiProperty(
        description: 'The public identifier of the company.',
        example: '/api/companies/PUBLIC_ID',
    )]
    private ?Company $company = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Url]
    #[Groups(['offer:read', 'offer:write'])]
    #[ApiProperty(
        description: 'Original URL of the offer',
        example: 'https://www.example.com/source-url',
    )]
    #[Assert\Length(
        min: 5,
        max: 180,
        minMessage: 'The offer origin url must be at least {{ limit }} characters long',
        maxMessage: 'The offer origin url code be longer than {{ limit }} characters',
    )]

    private ?string $url = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['offer:read', 'offer:write'])]
    private ?array $tag = null;

    #[ORM\Column(length: 40)]
    #[Groups(['offer:read', 'offer:write'])]
    #[Assert\Choice(choices: [
        PublicationStatus::Published->value,
        PublicationStatus::Archived->value,
    ], message: 'Invalid status')]
    #[ApiFilter(SearchFilter::class, strategy: 'exact')]
    private ?string $status = null;

    #[ORM\Column]
    #[ApiFilter(OrderFilter::class)]
    #[Groups(['offer:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['offer:read'])]
    #[ApiProperty(description: 'The expiration date of the offer\'s validity.')]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(length: 255)]
    #[Groups(['offer:read'])]
    private ?string $slug = null;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'offers')]
    #[Groups(['offer:read', 'offer:write'])]
    #[ApiProperty(
        example: ['/api/categories/PUBLIC_ID'],
    )]
    private Collection $category;

    #[ORM\Column]
    #[Groups(['offer:read', 'offer:write'])]
    #[ApiFilter(BooleanFilter::class, strategy: 'exact')]
    private ?bool $premium = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $externalId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $serviceName = null;

    public function __construct()
    {
        $this->category = new ArrayCollection();
    }

    #[Groups(['offer:read'])]
    #[ApiProperty(description: 'The number of days remaining until the offer\'s validity expires.' )]
    public function getDayLast(): ?int
    {
        if ($this->endDate === null) {
            return null;
        }

        $now = new \DateTimeImmutable();
        $interval = $now->diff($this->endDate);

        return $interval->days;
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

    public function isPremium(): ?bool
    {
        return $this->premium;
    }

    public function setPremium(bool $premium): static
    {
        $this->premium = $premium;

        return $this;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): static
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function getServiceName(): ?string
    {
        return $this->serviceName;
    }

    public function setServiceName(?string $serviceName): static
    {
        $this->serviceName = $serviceName;

        return $this;
    }
}
