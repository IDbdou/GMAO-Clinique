<?php

namespace App\Filament\Agent\Resources\Signalements\Schemas;

use App\Enums\PrioriteIntervention;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SignalementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('equipement_id')
                    ->label('Équipement concerné')
                    ->relationship('equipement', 'nom')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('priorite')
                    ->label('Niveau d’urgence perçu')
                    ->options(PrioriteIntervention::class)
                    ->default(PrioriteIntervention::Normale)
                    ->required()
                    ->native(false),

                Textarea::make('description')
                    ->label('Description de la panne')
                    ->placeholder('Décrivez le problème constaté…')
                    ->rows(5)
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
