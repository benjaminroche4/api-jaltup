<?php

namespace App\Factory;

use App\Entity\Category;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Category>
 */
final class CategoryFactory extends PersistentProxyObjectFactory
{
    private const CATEGORY_NAME = [
        'Développement Web', 'Développement Mobile', 'Science des Données', 'Apprentissage Automatique',
        'Intelligence Artificielle', 'Cybersécurité', 'Informatique en Nuage', 'DevOps',
        'Blockchain', 'Internet des Objets (IoT)', 'Informatique Quantique', 'Big Data',
        'Réalité Augmentée', 'Réalité Virtuelle', 'Développement de Jeux', 'Ingénierie Logicielle',
        'Gestion de Projet', 'Marketing Digital', 'Finance', 'Ressources Humaines',
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
        return Category::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->randomElement(self::CATEGORY_NAME),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Category $category): void {})
        ;
    }
}
