<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StatutEquipement: string implements HasLabel, HasColor
{
    case EnService = 'en_service';
    case EnPanne = 'en_panne';
    case EnMaintenance = 'en_maintenance';
    case Reforme = 'reforme';

    public function getLabel(): string
    {
        return match ($this) {
            self::EnService => 'En service',
            self::EnPanne => 'En panne',
            self::EnMaintenance => 'En maintenance',
            self::Reforme => 'Réformé',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::EnService => 'success',
            self::EnPanne => 'danger',
            self::EnMaintenance => 'warning',
            self::Reforme => 'gray',
        };
    }
}
