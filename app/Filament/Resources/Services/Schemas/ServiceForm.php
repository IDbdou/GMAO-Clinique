<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identification')
                    ->columns(2)
                    ->schema([
                        TextInput::make('nom')
                            ->label('Nom du service')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ex : Radiologie'),

                        TextInput::make('code')
                            ->label('Code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(10)
                            ->placeholder('Ex : RAD'),

                        TextInput::make('localisation')
                            ->label('Localisation')
                            ->maxLength(255)
                            ->placeholder('Ex : Aile Est, 1er étage'),
                    ]),

                Section::make('Responsables')
                    ->columns(2)
                    ->schema([
                        Select::make('chef_id')
                            ->label('Chef de service')
                            ->relationship(
                                'chef',
                                'name',
                                fn (Builder $query) => $query->whereHas('roles', fn (Builder $q) => $q->where('name', 'Chef de service'))
                            )
                            ->searchable()
                            ->preload()
                            ->placeholder('Non assigné'),

                        Select::make('adjoint_id')
                            ->label('Adjoint')
                            ->relationship(
                                'adjoint',
                                'name',
                                fn (Builder $query) => $query->whereHas('roles', fn (Builder $q) => $q->where('name', 'Chef de service'))
                            )
                            ->searchable()
                            ->preload()
                            ->placeholder('Non assigné')
                            ->native(false),
                    ]),

                Section::make('Statut')
                    ->schema([
                        Toggle::make('actif')
                            ->label('Actif')
                            ->default(true)
                            ->helperText('Décocher pour désactiver le service sans supprimer son historique.'),
                    ]),
            ]);
    }
}
