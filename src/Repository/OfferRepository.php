<?php

namespace App\Repository;

use App\Entity\Offer;
use App\Enum\PublicationStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Offer>
 */
class OfferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Offer::class);
    }

    /**
     * Find the expired offers.
     *
     * @return Offer[]
     */
    public function findExpiredOffers(): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.endDate < :now')
            ->andWhere('o.status != :expired')
            ->setParameter('now', new \DateTime())
            ->setParameter('expired', PublicationStatus::Expired->value)
            ->getQuery()
            ->getResult();
    }
}
