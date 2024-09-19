<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\OfferCount;
use App\Enum\PublicationStatus;
use App\Repository\OfferRepository;

/**
 * Provides the total number of offers.
 *
 * @implements ProviderInterface<OfferCount>
 */
readonly class OfferCountProvider implements ProviderInterface
{
    public function __construct(
        private OfferRepository $offerRepository)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): OfferCount
    {
        return new OfferCount($this->offerRepository->count([
            'status' => PublicationStatus::Published,
        ]));
    }
}
