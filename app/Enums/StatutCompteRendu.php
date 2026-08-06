<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StatutCompteRendu: string implements HasLabel, HasColor
{
    case Brouillon = 'brouillon';
    case Soumis = 'soumis';
    case Valide = 'valide';
    case Refuse = 'refuse';

    public function getLabel(): string
    {
        return match ($this) {
            self::Brouillon => 'Brouillon',
            self::Soumis => 'Soumis au chef de service',
            self::Valide => 'Validé par le chef de service',
            self::Refuse => 'Refusé',
        };
    }

    public function getColor(): string|array
    {
        return match ($this) {
            self::Brouillon => 'gray',
            self::Soumis => 'info',
            self::Valide => 'success',
            self::Refuse => 'danger',
        };
    }
}
