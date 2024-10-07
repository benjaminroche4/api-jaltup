<?php

namespace App\Factory;

use App\Entity\Category;
use App\Entity\Offer;
use App\Enum\ContractType;
use App\Enum\PublicationStatus;
use App\Enum\StudyLevel;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Offer>
 */
final class OfferFactory extends PersistentProxyObjectFactory
{
    private const OFFER_TITLE = [
        'Développeur Web Full Stack', 'Ingénieur en Cybersécurité',
        'Chef de Projet Informatique', 'Data Scientist',
        'Consultant en Transformation Digitale', 'Spécialiste en Apprentissage Automatique',
        'Architecte Cloud', 'Technicien Réseau et Système',
        'Analyste Financier', 'Responsable Marketing Digital',
        'Consultant en Ressources Humaines', 'Expert en Blockchain',
        'Ingénieur en Intelligence Artificielle', 'Gestionnaire de Projet Agile',
        'Technicien Support IT', 'Consultant SEO/SEA',
        'Chargé de Communication', 'Comptable Senior',
        'Juriste d’Entreprise', 'Responsable Logistique',
    ];
    private const OFFER_TAGS = [
        'Développement Web', 'Cybersécurité', 'Gestion de Projet', 'Science des Données',
        'Transformation Digitale', 'Apprentissage Automatique', 'Cloud Computing', 'Réseaux et Systèmes',
        'Finance', 'Marketing Digital', 'Ressources Humaines', 'Blockchain',
        'Intelligence Artificielle', 'Agilité', 'Support IT', 'SEO/SEA',
        'Communication', 'Comptabilité', 'Droit des Entreprises', 'Logistique',
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
        return Offer::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        return [
            'title' => self::faker()->randomElement(self::OFFER_TITLE),
            'place' => [
                'fullAddress' => self::faker()->address(),
                'city' => self::faker()->city(),
                'zipCode' => self::faker()->postcode(),
                'latitude' => self::faker()->latitude(),
                'longitude' => self::faker()->longitude(),
            ],
            'job' => [
                'description' => self::faker()->text(),
                'contractType' => self::faker()->randomElement([ContractType::FixedTerm->value, ContractType::Permanent->value, ContractType::Apprenticeship->value, ContractType::Professionalization->value]),
                'duration' => self::faker()->numberBetween(1, 12),
                'remote' => self::faker()->boolean(),
                'studyLevel' => self::faker()->randomElement([StudyLevel::NoDiploma->value, StudyLevel::CAP->value, StudyLevel::BAC->value, StudyLevel::BAC2->value]),
                'startDate' => self::faker()->dateTimeBetween('+1 week', '+1 month')->format('Y-m-d'),
            ],
            'url' => self::faker()->url(),
            'tag' => self::faker()->randomElements(self::OFFER_TAGS, self::faker()->numberBetween(1, 5)),
            'status' => self::faker()->randomElement([PublicationStatus::Published->value, PublicationStatus::Expired->value]),
            'premium' => self::faker()->boolean(),
            'category' => CategoryFactory::createMany(1),
            'company' => CompanyFactory::new(),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-15 days')),
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
