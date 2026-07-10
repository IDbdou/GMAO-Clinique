<?php

namespace App\Filament\Agent\Resources\Signalements\Tables;

use App\Enums\StatutIntervention;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SignalementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('date_demande', 'desc')
            ->columns([
                TextColumn::make('equipement.nom')
                    ->label('Équipement')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->wrap()
                    ->tooltip(fn (TextColumn $column): ?string => $column->getState()),

                TextColumn::make('priorite')
                    ->label('Urgence')
                    ->badge()
                    ->sortable(),

                TextColumn::make('statut')
                    ->label('Statut actuel')
                    ->badge()
                    ->sortable(),

                TextColumn::make('technicien.name')
                    ->label('Pris en charge par')
                    ->placeholder('En attente')
                    ->toggleable(),

                TextColumn::make('date_demande')
                    ->label('Signalé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('statut')
                    ->label('Statut')
                    ->options(StatutIntervention::class),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
