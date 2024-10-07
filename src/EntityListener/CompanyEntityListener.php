<?php

namespace App\EntityListener;

use App\Entity\Category;
use App\Entity\Company;
use App\Service\IdGeneratorService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * The EntityListener of the Company entity.
 *
 * @AsEntityListener(event="prePersist", entity=Category::class)
 */
#[AsEntityListener(event: Events::prePersist, entity: Company::class)]
#[AsEntityListener(event: Events::preUpdate, entity: Company::class)]
readonly class CompanyEntityListener
{
    public function __construct(
        private SluggerInterface $slugger,
    ) {
    }

    public function prePersist(Company $company, LifecycleEventArgs $args): void
    {
        $company->setSlug($this->slugger->slug($company->getName())->lower()->toString());
        $company->setPublicId(IdGeneratorService::generateUniqueId(6));
        if ($company->getCreatedAt() === null) {
            $company->setCreatedAt(new \DateTimeImmutable());
        }
    }

    public function preUpdate(Company $company, LifecycleEventArgs $args): void
    {
        $company->setSlug($this->slugger->slug($company->getName())->lower()->toString());
    }
}
