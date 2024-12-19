<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * The category entity.
 */
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    description: 'Category of the job resource.',
    operations: [
        new Get(
            uriTemplate: '/categories/{publicId}',
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
        'groups' => ['category:read'],
    ],
    denormalizationContext: [
        'groups' => ['category:write'],
    ],
    paginationClientItemsPerPage: true,
    paginationMaximumItemsPerPage: 100,
)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 20, unique: true)]
    #[Groups(['category:read'])]
    #[Assert\Unique]
    #[ApiProperty(description: 'The public identifier of the category.', )]
    private ?string $publicId = null;

    #[ORM\Column(length: 120)]
    #[Groups(['category:read', 'category:write', 'offer:read', 'user:read'])]
    #[ApiFilter(SearchFilter::class, strategy: 'exact')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'The category name must be at least {{ limit }} characters long',
        maxMessage: 'The category name cannot be longer than {{ limit }} characters',
    )]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['category:read'])]
    private ?string $slug = null;

    /**
     * @var Collection<int, Offer>
     */
    #[ORM\ManyToMany(targetEntity: Offer::class, mappedBy: 'category')]
    #[Groups(['category:read'])]
    private Collection $offers;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'userInterest')]
    private Collection $users;

    public function __construct()
    {
        $this->offers = new ArrayCollection();
        $this->users = new ArrayCollection();
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
            $offer->addCategory($this);
        }

        return $this;
    }

    public function removeOffer(Offer $offer): static
    {
        if ($this->offers->removeElement($offer)) {
            $offer->removeCategory($this);
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

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addUserInterest($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeUserInterest($this);
        }

        return $this;
    }
}
