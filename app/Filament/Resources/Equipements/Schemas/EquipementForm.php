<?php

namespace App\Filament\Resources\Equipements\Schemas;

use App\Enums\Criticite;
use App\Enums\StatutEquipement;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EquipementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identification')
                    ->columns(2)
                    ->schema([
                        TextInput::make('nom')
                            ->label('Nom')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('code_inventaire')
                            ->label('Code inventaire')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('numero_serie')
                            ->label('N° de série')
                            ->maxLength(255),

                        TextInput::make('fournisseur')
                            ->label('Fournisseur')
                            ->maxLength(255),

                        TextInput::make('marque')
                            ->label('Marque')
                            ->maxLength(255),

                        TextInput::make('modele')
                            ->label('Modèle')
                            ->maxLength(255),
                    ]),

                Section::make('Affectation & état')
                    ->columns(2)
                    ->schema([
                        TextInput::make('service')
                            ->label('Service')
                            ->placeholder('Ex : Radiologie, Hémodialyse, Bloc opératoire…')
                            ->maxLength(255),

                        TextInput::make('localisation')
                            ->label('Localisation')
                            ->placeholder('Salle / emplacement')
                            ->maxLength(255),

                        Select::make('criticite')
                            ->label('Criticité')
                            ->options(Criticite::class)
                            ->default(Criticite::Moyenne)
                            ->required()
                            ->native(false),

                        Select::make('statut')
                            ->label('Statut')
                            ->options(StatutEquipement::class)
                            ->default(StatutEquipement::EnService)
                            ->required()
                            ->native(false),

                        DatePicker::make('date_mise_en_service')
                            ->label('Date de mise en service')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                    ]),

                Section::make('Notes')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
