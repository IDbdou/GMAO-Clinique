<?php

namespace App\Filament\Resources\Interventions\Tables;

use App\Enums\PrioriteIntervention;
use App\Enums\StatutIntervention;
use App\Enums\TypeIntervention;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InterventionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('date_demande', 'desc')
            ->columns([
                TextColumn::make('titre')
                    ->label('Titre')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('equipement.nom')
                    ->label('Équipement')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->sortable(),

                TextColumn::make('priorite')
                    ->label('Priorité')
                    ->badge()
                    ->sortable(),

                TextColumn::make('statut')
                    ->label('Statut')
                    ->badge()
                    ->sortable(),

                TextColumn::make('technicien.name')
                    ->label('Technicien')
                    ->placeholder('Non assigné')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('date_demande')
                    ->label('Demandée le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('cout')
                    ->label('Coût')
                    ->money('MAD')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->options(TypeIntervention::class),

                SelectFilter::make('priorite')
                    ->label('Priorité')
                    ->options(PrioriteIntervention::class),

                SelectFilter::make('statut')
                    ->label('Statut')
                    ->options(StatutIntervention::class),

                SelectFilter::make('technicien')
                    ->label('Technicien')
                    ->relationship('technicien', 'name'),

                SelectFilter::make('equipement')
                    ->label('Équipement')
                    ->relationship('equipement', 'nom')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
