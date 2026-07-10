<?php

namespace App\Filament\Resources\Equipements\Tables;

use App\Enums\Criticite;
use App\Enums\StatutEquipement;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EquipementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code_inventaire')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('service')
                    ->label('Service')
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('criticite')
                    ->label('Criticité')
                    ->badge()
                    ->sortable(),

                TextColumn::make('statut')
                    ->label('Statut')
                    ->badge()
                    ->sortable(),

                TextColumn::make('interventions_count')
                    ->label('Interv.')
                    ->counts('interventions')
                    ->badge()
                    ->color('gray')
                    ->toggleable(),

                TextColumn::make('date_mise_en_service')
                    ->label('Mise en service')
                    ->date('d/m/Y')
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('service')
                    ->label('Service')
                    ->options(fn (): array => \App\Models\Equipement::query()
                        ->whereNotNull('service')
                        ->distinct()
                        ->orderBy('service')
                        ->pluck('service', 'service')
                        ->all()),

                SelectFilter::make('criticite')
                    ->label('Criticité')
                    ->options(Criticite::class),

                SelectFilter::make('statut')
                    ->label('Statut')
                    ->options(StatutEquipement::class),
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
