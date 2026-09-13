<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum FrequencePreventive: string implements HasLabel, HasColor
{
    case Mensuelle = 'mensuelle';
    case Trimestrielle = 'trimestrielle';
    case Semestrielle = 'semestrielle';
    case Annuelle = 'annuelle';

    public function getLabel(): string
    {
        return match ($this) {
            self::Mensuelle => 'Mensuelle',
            self::Trimestrielle => 'Trimestrielle',
            self::Semestrielle => 'Semestrielle',
            self::Annuelle => 'Annuelle',
        };
    }

    public function getColor(): string|array
    {
        return match ($this) {
            self::Mensuelle => 'info',
            self::Trimestrielle => 'warning',
            self::Semestrielle => 'primary',
            self::Annuelle => 'success',
        };
    }

    /**
     * Calcule la prochaine date d'exécution à partir d'une date de référence.
     */
    public function prochaineDate(?\Carbon\Carbon $reference = null): \Carbon\Carbon
    {
        $ref = $reference?->clone() ?? now();

        return match ($this) {
            self::Mensuelle => $ref->addMonth(),
            self::Trimestrielle => $ref->addMonths(3),
            self::Semestrielle => $ref->addMonths(6),
            self::Annuelle => $ref->addYear(),
        };
    }
}
