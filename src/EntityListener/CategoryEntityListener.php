<?php

namespace App\EntityListener;

use App\Entity\Category;
use App\Service\IdGeneratorService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * The EntityListener of the Category entity.
 *
 * @AsEntityListener(event="prePersist", entity=Category::class)
 */
#[AsEntityListener(event: Events::prePersist, entity: Category::class)]
#[AsEntityListener(event: Events::preUpdate, entity: Category::class)]
readonly class CategoryEntityListener
{
    public function __construct(
        private SluggerInterface $slugger,
    ) {
    }

    public function prePersist(Category $category, LifecycleEventArgs $args): void
    {
        $category->setSlug($this->slugger->slug($category->getName())->lower()->toString());
        $category->setPublicId(IdGeneratorService::generateUniqueId(6));
    }

    public function preUpdate(Category $category, LifecycleEventArgs $args): void
    {
        $this->slugger->slug($category->getName())->lower()->toString();
    }
}
