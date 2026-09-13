<?php

namespace App\Filament\Resources\CompteRendus\Schemas;

use App\Enums\StatutCompteRendu;
use App\Models\Intervention;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class CompteRenduForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identification')
                    ->description('Intervention concernée et technicien responsable')
                    ->icon('heroicon-m-identification')
                    ->columns(2)
                    ->schema([
                        Select::make('intervention_id')
                            ->label('Intervention')
                            ->relationship(
                                'intervention',
                                'titre',
                                fn (Builder $query) => $query->whereIn('statut', ['nouveau', 'ouverte', 'en_cours', 'en_attente'])
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn (string $operation): bool => $operation === 'edit')
                            ->dehydrated(),

                        Select::make('technicien_id')
                            ->label('Technicien')
                            ->relationship(
                                'technicien',
                                'name',
                                fn (Builder $query) => $query->whereHas('roles', fn (Builder $q) => $q->where('name', 'Technicien'))
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->default(auth()->id()),

                        Hidden::make('statut')
                            ->default(StatutCompteRendu::Brouillon->value),
                    ]),

                Section::make('Questionnaire technique')
                    ->description('Détails de l\'intervention et coûts associés')
                    ->icon('heroicon-m-wrench-screwdriver')
                    ->columns(2)
                    ->schema([
                        Textarea::make('observations')
                            ->label('Travail réalisé / Observations')
                            ->placeholder('Décrivez les actions réalisées, diagnostic, réparations effectuées...')
                            ->rows(4)
                            ->columnSpanFull()
                            ->required(),

                        Textarea::make('pieces_utilisees')
                            ->label('Pièces détachées utilisées')
                            ->placeholder('Ex : tube RX, filtre, capteur...')
                            ->rows(3)
                            ->columnSpanFull(),

                        TextInput::make('temps_passe')
                            ->label('Temps passé (heures)')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.5)
                            ->default(1)
                            ->required(),

                        TextInput::make('cout_main_oeuvre')
                            ->label('Coût main d\'œuvre (MAD)')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('MAD'),

                        TextInput::make('cout_pieces')
                            ->label('Coût pièces détachées (MAD)')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('MAD'),

                        TextInput::make('cout_total')
                            ->label('Coût total (MAD)')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('MAD')
                            ->hint('Calcul automatique possible.'),
                    ]),

                Section::make('Signature numérique du technicien')
                    ->description('Identification du rédacteur et horodatage')
                    ->icon('heroicon-m-pencil-square')
                    ->schema([
                        TextInput::make('signature_technicien')
                            ->label('Nom et prénom du technicien')
                            ->required()
                            ->default(auth()->user()?->name)
                            ->maxLength(255),

                        DateTimePicker::make('date_soumission')
                            ->label('Date de soumission')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->default(now())
                            ->required(),
                    ]),
            ]);
    }
}
