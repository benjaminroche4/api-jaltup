<?php

namespace App\Enum;

/**
 * Enum ContractType
 */
enum ContractType: string {
    case FixedTerm = 'fixed_term'; // CDD
    case Permanent = 'permanent'; // CDI
    case Apprenticeship = 'apprenticeship'; // Alternance
    case Professionalization = 'professionalization'; // Contrat de professionnalisation
}