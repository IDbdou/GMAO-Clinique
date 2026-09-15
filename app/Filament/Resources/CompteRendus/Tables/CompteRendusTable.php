<?php

namespace App\Filament\Resources\CompteRendus\Tables;

use App\Enums\StatutCompteRendu;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CompteRendusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('intervention.titre')
                    ->label('Intervention')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('intervention.equipement.nom')
                    ->label('Équipement')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('intervention.service.nom')
                    ->label('Service')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('technicien.name')
                    ->label('Technicien')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('statut')
                    ->label('Statut')
                    ->badge()
                    ->sortable(),

                TextColumn::make('temps_passe')
                    ->label('Temps')
                    ->suffix(' h')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('cout_total')
                    ->label('Coût total')
                    ->money('MAD')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('date_soumission')
                    ->label('Soumis le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('signature_chef_service')
                    ->label('Validé par')
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('statut')
                    ->label('Statut')
                    ->options(StatutCompteRendu::class),

                SelectFilter::make('technicien')
                    ->label('Technicien')
                    ->relationship('technicien', 'name'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
