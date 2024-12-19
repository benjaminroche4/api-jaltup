<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * The company entity.
 */
#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    description: 'Get the company information.',
    operations: [
        new Get(
            uriTemplate: '/companies/{publicId}',
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
        'groups' => ['company:read'],
    ],
    denormalizationContext: [
        'groups' => ['company:write'],
    ],
    paginationClientItemsPerPage: true,
    paginationMaximumItemsPerPage: 100,
)]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 20, unique: true)]
    #[Groups(['company:read'])]
    #[Assert\Unique]
    #[ApiProperty(description: 'The public identifier of the company.', )]
    private ?string $publicId = null;

    #[ORM\Column(length: 120)]
    #[ApiFilter(SearchFilter::class, strategy: 'exact')]
    #[Groups(['company:read', 'company:write', 'offer:read', 'category:read'])]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'The company name must be at least {{ limit }} characters long',
        maxMessage: 'The company name cannot be longer than {{ limit }} characters',
    )]
    private ?string $name = null;

    #[ORM\Column(length: 14, nullable: true)]
    #[ApiFilter(SearchFilter::class, strategy: 'exact')]
    #[Groups(['company:read', 'company:write'])]
    #[Assert\Length(
        min: 8,
        max: 17,
        minMessage: 'The company siret must be at least {{ limit }} characters long',
        maxMessage: 'The company siret cannot be longer than {{ limit }} characters',
    )]
    #[ApiProperty(
        example: 'string',
    )]
    private ?string $siret = null;

    #[ORM\Column(length: 50)]
    #[Groups(['company:read', 'company:write'])]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'The company head office city must be at least {{ limit }} characters long',
        maxMessage: 'The company head office city cannot be longer than {{ limit }} characters',
    )]
    private ?string $headOfficeCity = null;

    #[ORM\Column(length: 120, nullable: true)]
    #[Assert\Email]
    #[Groups(['company:read', 'company:write'])]
    #[Assert\Length(
        min: 5,
        max: 50,
        minMessage: 'The company email must be at least {{ limit }} characters long',
        maxMessage: 'The company email cannot be longer than {{ limit }} characters',
    )]
    private ?string $contactEmail = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['company:read', 'company:write'])]
    #[Assert\Length(
        min: 8,
        max: 16,
        minMessage: 'The company phone number must be at least {{ limit }} characters long',
        maxMessage: 'The company phone number cannot be longer than {{ limit }} characters',
    )]
    #[ApiProperty(
        example: 'string',
    )]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Url]
    #[Groups(['company:read', 'company:write', 'offer:read'])]
    #[Assert\Length(
        min: 5,
        max: 50,
        minMessage: 'The company website url must be at least {{ limit }} characters long',
        maxMessage: 'The company website url cannot be longer than {{ limit }} characters',
    )]
    private ?string $websiteUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['company:read', 'company:write', 'offer:read'])]
    private ?string $logo = null;

    #[ORM\Column]
    #[Groups(['company:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255)]
    #[Groups(['company:read'])]
    private ?string $slug = null;

    /**
     * @var Collection<int, Offer>
     */
    #[Groups(['company:read'])]
    #[ORM\OneToMany(targetEntity: Offer::class, mappedBy: 'company')]
    private Collection $offers;

    #[ORM\Column]
    #[Groups(['company:read', 'company:write', 'offer:read'])]
    private ?bool $verified = null;

    #[Groups(['company:read', 'offer:read'])]
    #[ApiProperty(description: 'The number of offers for the company.')]
    public function getOfferNumber(): int
    {
        return $this->offers->count();
    }

    public function __construct()
    {
        $this->offers = new ArrayCollection();
    }

    public function getId(): ?int
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

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): static
    {
        $this->siret = $siret;

        return $this;
    }

    public function getContactEmail(): ?string
    {
        return $this->contactEmail;
    }

    public function setContactEmail(?string $contactEmail): static
    {
        $this->contactEmail = $contactEmail;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getWebsiteUrl(): ?string
    {
        return $this->websiteUrl;
    }

    public function setWebsiteUrl(?string $websiteUrl): static
    {
        $this->websiteUrl = $websiteUrl;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;

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
     * @return Collection<int, Offer>
     */
    public function getOffers(): Collection
    {
        return $this->offers;
    }

    public function addOffer(Offer $offer): static
    {
        if (!$this->offers->contains($offer)) {
            $this->offers->add($offer);
            $offer->setCompany($this);
        }

        return $this;
    }

    public function removeOffer(Offer $offer): static
    {
        if ($this->offers->removeElement($offer)) {
            // set the owning side to null (unless already changed)
            if ($offer->getCompany() === $this) {
                $offer->setCompany(null);
            }
        }

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

    public function isVerified(): ?bool
    {
        return $this->verified;
    }

    public function setVerified(bool $verified): static
    {
        $this->verified = $verified;

        return $this;
    }

    public function getHeadOfficeCity(): ?string
    {
        return $this->headOfficeCity;
    }

    public function setHeadOfficeCity(string $headOfficeCity): static
    {
        $this->headOfficeCity = $headOfficeCity;

        return $this;
    }
}
