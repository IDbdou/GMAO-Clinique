<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TypeIntervention: string implements HasLabel, HasColor
{
    case Curatif = 'curatif';
    case Preventif = 'preventif';

    public function getLabel(): string
    {
        return match ($this) {
            self::Curatif => 'Curatif',
            self::Preventif => 'Préventif',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Curatif => 'danger',
            self::Preventif => 'info',
        };
    }
}
