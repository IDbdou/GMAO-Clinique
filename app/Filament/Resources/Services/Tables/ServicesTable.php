<?php

namespace App\Filament\Resources\Services\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('chef.name')
                    ->label('Chef de service')
                    ->placeholder('—'),

                TextColumn::make('adjoint.name')
                    ->label('Adjoint')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('equipements_count')
                    ->label('Équipements')
                    ->counts('equipements')
                    ->badge()
                    ->color('gray')
                    ->toggleable(),

                TextColumn::make('interventions_count')
                    ->label('Interventions')
                    ->counts('interventions')
                    ->badge()
                    ->color('gray')
                    ->toggleable(),

                IconColumn::make('actif')
                    ->label('Actif')
                    ->boolean()
                    ->sortable(),
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
