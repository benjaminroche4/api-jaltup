<?php

namespace App\EntityListener;

use App\Entity\Offer;
use App\Entity\User;
use App\Service\IdGeneratorService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

/**
 * The EntityListener of the User entity.
 *
 * @AsEntityListener(event="prePersist", entity=Category::class)
 */
#[AsEntityListener(event: Events::prePersist, entity: User::class)]
#[AsEntityListener(event: Events::preUpdate, entity: User::class)]
readonly class UserEntityListener
{
    public function __construct(
    ) {
    }

    public function prePersist(User $user, LifecycleEventArgs $args): void
    {
        $user->setPublicId(IdGeneratorService::generateUniqueId());
        $user->setRoles(['ROLE_USER']);
        $user->setProfilePicture('https://api.dicebear.com/9.x/thumbs/svg?seed=' . $user->getFirstName());
        if ($user->getCreatedAt() === null) {
            $user->setCreatedAt(new \DateTimeImmutable());
        }
    }

    public function preUpdate(Offer $offer, LifecycleEventArgs $args): void
    {
    }
}
