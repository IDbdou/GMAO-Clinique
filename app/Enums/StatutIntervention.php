<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StatutIntervention: string implements HasLabel, HasColor
{
    case Ouverte = 'ouverte';
    case EnCours = 'en_cours';
    case EnAttente = 'en_attente';
    case Terminee = 'terminee';
    case Annulee = 'annulee';

    public function getLabel(): string
    {
        return match ($this) {
            self::Ouverte => 'Ouverte',
            self::EnCours => 'En cours',
            self::EnAttente => 'En attente',
            self::Terminee => 'Terminée',
            self::Annulee => 'Annulée',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Ouverte => 'info',
            self::EnCours => 'warning',
            self::EnAttente => 'gray',
            self::Terminee => 'success',
            self::Annulee => 'danger',
        };
    }
}
