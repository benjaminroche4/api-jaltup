<?php

namespace App\Enum;

/**
 * Enum StudyLevel
 */
enum StudyLevel: string {
    case NoDiploma = 'no_diploma'; // Sans diplôme
    case BEP = 'bep'; // BEP
    case CAP = 'cap'; // CAP
    case BAC = 'bac'; // BAC
    case BAC2 = 'bac+2'; // BAC+2
    case BAC3 = 'bac+3'; // BAC+3
    case BAC4 = 'bac+4'; // BAC+4
    case BAC5 = 'bac+5'; // BAC+5
}