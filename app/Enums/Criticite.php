<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum Criticite: string implements HasLabel, HasColor
{
    case Faible = 'faible';
    case Moyenne = 'moyenne';
    case Haute = 'haute';
    case Critique = 'critique';

    public function getLabel(): string
    {
        return match ($this) {
            self::Faible => 'Faible',
            self::Moyenne => 'Moyenne',
            self::Haute => 'Haute',
            self::Critique => 'Critique',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Faible => 'gray',
            self::Moyenne => 'info',
            self::Haute => 'warning',
            self::Critique => 'danger',
        };
    }
}
