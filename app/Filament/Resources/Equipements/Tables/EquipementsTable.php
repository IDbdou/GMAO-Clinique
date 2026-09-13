<?php

namespace App\Filament\Resources\Equipements\Tables;

use App\Enums\Criticite;
use App\Enums\StatutEquipement;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class EquipementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('nom', 'asc')
            ->groups([
                Group::make('service.nom')
                    ->getTitleFromRecordUsing(fn ($record) => $record->service?->nom ?? 'Sans service')
                    ->collapsible()
                    ->getDescriptionFromRecordUsing(fn ($record): string => 'Localisation : ' . ($record->localisation ?? '—')),
            ])
            ->columns([
                TextColumn::make('code_inventaire')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('marque')
                    ->label('Marque')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('modele')
                    ->label('Modèle')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('service.nom')
                    ->label('Service')
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('localisation')
                    ->label('Localisation')
                    ->placeholder('—')
                    ->sortable()
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
                    ->label('Interventions')
                    ->counts('interventions')
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('date_mise_en_service')
                    ->label('Mise en service')
                    ->date('d/m/Y')
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('fournisseur')
                    ->label('Fournisseur')
                    ->searchable()
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('service')
                    ->label('Service')
                    ->relationship('service', 'nom'),

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
