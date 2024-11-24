<?php

namespace App\Factory;

use App\Entity\User;
use App\Enum\StudyLevel;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    const USER_ROLES = ['ROLE_USER', 'ROLE_ADMIN'];

    private $passwordHasher;

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();

        $this->passwordHasher = $passwordHasher;
    }

    public static function class(): string
    {
        return User::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        return [
            'email' => self::faker()->unique()->email(),
            'password' => 'password',
            'roles' => [self::faker()->randomElement(self::USER_ROLES)],
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-5 days')),
            'firstName' => self::faker()->firstName(),
            'lastName' => self::faker()->lastName(),
            'referralCode' => self::faker()->unique()->regexify('[A-Z0-9]{6}'),
            'profilePicture' => 'https://api.dicebear.com/9.x/thumbs/svg?seed=' . self::faker()->firstName(),
            'study' => [
                'level' => self::faker()->randomElement([StudyLevel::NoDiploma->value, StudyLevel::CAP->value, StudyLevel::BAC->value, StudyLevel::BAC2->value]),
                'city' => self::faker()->city,
                'school' => self::faker()->company(),
            ],
            'userInterest' => CategoryFactory::createMany(3),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this->afterInstantiate(function(User $user) {
                $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
            });
    }
}
