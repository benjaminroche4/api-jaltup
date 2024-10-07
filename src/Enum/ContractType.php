<?php

namespace App\Enum;

/**
 * Enum ContractType.
 */
enum ContractType: string
{
    case FixedTerm = 'CDD'; // CDD
    case Permanent = 'CDI'; // CDI
    case Apprenticeship = 'Alternance'; // Alternance
    case Professionalization = 'Professionalisation'; // Contrat de professionalisation
}
