<?php

namespace App\EntityListener;

use App\Entity\Category;
use App\Entity\Company;
use App\Entity\Offer;
use App\Enum\PublicationStatus;
use App\Service\IdGeneratorService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * The EntityListener of the Offer entity.
 * @AsEntityListener(event="prePersist", entity=Category::class)
 */
#[AsEntityListener(event: Events::prePersist, entity: Offer::class)]
#[AsEntityListener(event: Events::preUpdate, entity: Offer::class)]
readonly class OfferEntityListener
{
    public function __construct(
        private SluggerInterface $slugger
    )
    {
    }

    public function prePersist(Offer $offer, LifecycleEventArgs $args):void
    {
        $offer->setSlug($this->slugger->slug($offer->getTitle())->lower()->toString());
        $offer->setPublicId(IdGeneratorService::generateUniqueId(6));
        $offer->setCreatedAt(new \DateTimeImmutable());
        //By default, the offer is valid for one month
        $offer->setEndDate(new \DateTimeImmutable('+1 month'));
    }

    public function preUpdate(Offer $offer, LifecycleEventArgs $args):void
    {
        $offer->setSlug($this->slugger->slug($offer->getTitle())->lower()->toString());
    }
}