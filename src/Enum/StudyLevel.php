<?php

namespace App\Enum;

/**
 * Enum StudyLevel.
 */
enum StudyLevel: string
{
    case NoDiploma = 'Sans diplôme'; // Sans diplôme
    case BEP = 'BEP'; // BEP
    case CAP = 'CAP'; // CAP
    case BAC = 'Bac'; // BAC
    case BAC2 = 'Bac+2'; // BAC+2
    case BAC3 = 'Bac+3'; // BAC+3
    case BAC4 = 'Bac+4'; // BAC+4
    case BAC5 = 'Bac+5'; // BAC+5
    case BAC8 = 'Bac+8'; // BAC+5
}
