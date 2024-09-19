<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;

/**
 * The DTO for the offer count.
 */
class OfferCount
{
    public function __construct(int $count)
    {
        $this->count = $count;
    }

    #[Groups(['offer:read'])]
    public int $count;
}
