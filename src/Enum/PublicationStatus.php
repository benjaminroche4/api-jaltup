<?php

namespace App\Enum;

/**
 * Enum PublicationStatus
 */
enum PublicationStatus: string {
    case Draft = 'draft'; //brouillon
    case Published = 'published'; //publié
    case Archived = 'archived'; //archivé
    case PendingReview = 'pending_review'; //en attente de relecture
    case Rejected = 'rejected'; //rejeté
    case Expired = 'expired'; //expiré
}
