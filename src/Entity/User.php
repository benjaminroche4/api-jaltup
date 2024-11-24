<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\SecurityController;
use App\Enum\StudyLevel;
use App\Repository\UserRepository;
use App\State\UserStateProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * The user entity.
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/users/{publicId}',
            uriVariables: ['publicId'],
            openapiContext: [
                'security' => [
                    ['access_token' => []],
                ],
            ],
            security: 'is_granted("ROLE_ADMIN")',
            securityMessage: 'You don\'t have permission to access this resource.',
        ),
        new GetCollection(
            openapiContext: [
                'security' => [
                    ['access_token' => []],
                ],
            ],
            security: 'is_granted("ROLE_ADMIN")',
            securityMessage: 'You don\'t have permission to access this resource.'
        ),
        new Post(
            uriTemplate: '/register',
            openapiContext: [
                'requestBody' => [
                    'content' => [
                        'application/ld+json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'email' => [
                                        'type' => 'string',
                                        'format' => 'email',
                                    ],
                                    'password' => [
                                        'type' => 'string',
                                        'format' => 'password',
                                    ],
                                    'firstName' => [
                                        'type' => 'string',
                                    ],
                                    'lastName' => [
                                        'type' => 'string',
                                    ],
                                    'referralCode' => [
                                        'type' => 'string',
                                    ],
                                    'study' => [
                                        'type' => 'json',
                                        'example' => [
                                            'school' => 'string',
                                            'city' => 'string',
                                            'level' => 'string',
                                        ]
                                    ],
                                    'userInterest' => [
                                        'type' => 'array',
                                        'example' => [
                                            '/api/categories/PUBLIC_ID',
                                        ]
                                    ]
                                ]
                            ],
                        ],
                    ],
                ],
            ],
        ),
        new Get(
            uriTemplate: '/me',
            openapiContext: [
                'security' => [
                    ['access_token' => []],
                ],
            ],
            security: 'is_granted("ROLE_USER")',
            securityMessage: 'You don\'t have permission to access this resource.',
            provider: UserStateProvider::class,
        ),
    ],
    normalizationContext: [
        'groups' => ['user:read'],
    ],
    denormalizationContext: [
        'groups' => ['user:write'],
    ],
    paginationItemsPerPage: 40,
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20, unique: true)]
    #[Assert\Unique]
    #[Groups(['user:read'])]
    #[ApiProperty(description: 'The public identifier of the user.', )]
    private ?string $publicId = null;

    #[ORM\Column(length: 180)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $email = null;

    /**
     * @var array The user roles
     */
    #[ORM\Column]
    #[Groups(['user:read', 'user:write'])]
    private array $roles = [];

    /**
     * @var string|null The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[Groups(['user:write'])]
    #[SerializedName('password')]
    private ?string $plainPassword = null;

    #[ORM\Column]
    #[Groups(['user:write', 'user:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 50)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 70)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $lastName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $referralCode = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read'])]
    private ?string $profilePicture = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    #[Assert\Collection(
        fields: [
            'school' => [
                new Assert\Type(type: 'string'),
            ],
            'city' => [
                new Assert\Type(type: 'string'),
            ],
            'level' => new Assert\Optional([
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
        ]
    )]
    private ?array $study = null;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'users')]
    #[Groups(['user:read', 'user:write'])]
    private Collection $userInterest;

    public function __construct()
    {
        $this->userInterest = new ArrayCollection();
    }

    public function getId(): ?int
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
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

    public function getPublicId(): ?string
    {
        return $this->publicId;
    }

    public function setPublicId(string $publicId): static
    {
        $this->publicId = $publicId;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getReferralCode(): ?string
    {
        return $this->referralCode;
    }

    public function setReferralCode(?string $referralCode): static
    {
        $this->referralCode = $referralCode;

        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(string $profilePicture): static
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    public function getStudy(): ?array
    {
        return $this->study;
    }

    public function setStudy(?array $study): static
    {
        $this->study = $study;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getUserInterest(): Collection
    {
        return $this->userInterest;
    }

    public function addUserInterest(Category $userInterest): static
    {
        if (!$this->userInterest->contains($userInterest)) {
            $this->userInterest->add($userInterest);
        }

        return $this;
    }

    public function removeUserInterest(Category $userInterest): static
    {
        $this->userInterest->removeElement($userInterest);

        return $this;
    }
}
