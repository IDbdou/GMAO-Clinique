<?php

namespace App\Filament\Agent\Resources\Signalements\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SignalementInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Mon signalement')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('equipement.nom')->label('Équipement'),
                        TextEntry::make('priorite')->label('Urgence perçue')->badge(),
                        TextEntry::make('description')->label('Description')->columnSpanFull(),
                        TextEntry::make('date_demande')->label('Signalé le')->dateTime('d/m/Y H:i'),
                    ]),

                Section::make('Suivi')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('statut')->label('Statut actuel')->badge(),
                        TextEntry::make('technicien.name')->label('Pris en charge par')->placeholder('En attente d’affectation'),
                        TextEntry::make('date_debut')->label('Début')->dateTime('d/m/Y H:i')->placeholder('—'),
                        TextEntry::make('date_fin')->label('Résolu le')->dateTime('d/m/Y H:i')->placeholder('—'),
                        TextEntry::make('rapport')->label('Compte-rendu du technicien')->placeholder('Aucun compte-rendu pour le moment')->columnSpanFull(),
                    ]),
            ]);
    }
}
