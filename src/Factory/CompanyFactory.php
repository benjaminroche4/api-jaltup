<?php

namespace App\Factory;

use App\Entity\Company;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Company>
 */
final class CompanyFactory extends PersistentProxyObjectFactory
{
    private const COMPANY_NAME = [
        'Google', 'Facebook', 'Amazon', 'EDF',
        'Total', 'Apple', 'Capgemini', 'Carrefour',
        'Gaumont', 'JCDecaux', 'La Poste', 'Laboratoires Expanscience',
        'SDV International Logistics', 'TF1 Group', 'Waterman',
    ];
    private const COMPANY_LOGO = [
        'edf.png', 'google.png', 'total.png', 'gaumont.webp', 'capgemini.png',
    ];

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Company::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->randomElement(self::COMPANY_NAME),
            'siret' => self::faker()->randomNumber(9),
            'headOfficeCity' => self::faker()->city(),
            'contactEmail' => self::faker()->email(),
            'phoneNumber' => self::faker()->phoneNumber(),
            'websiteUrl' => self::faker()->url(),
            'logo' => self::faker()->randomElement(self::COMPANY_LOGO),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-2 months')),
            'verified' => self::faker()->boolean(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Company $company): void {})
        ;
    }
}
