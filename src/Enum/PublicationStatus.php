<?php

namespace App\Enum;

/**
 * Enum PublicationStatus.
 */
enum PublicationStatus: string
{
    case Published = 'published';
    case Archived = 'archived';
    case Expired = 'expired';
}
