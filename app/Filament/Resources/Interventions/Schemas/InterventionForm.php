<?php

namespace App\Filament\Resources\Interventions\Schemas;

use App\Enums\PrioriteIntervention;
use App\Enums\StatutIntervention;
use App\Enums\TypeIntervention;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InterventionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Intervention')
                    ->columns(2)
                    ->schema([
                        Select::make('equipement_id')
                            ->label('Équipement')
                            ->relationship('equipement', 'nom')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('titre')
                            ->label('Titre')
                            ->required()
                            ->maxLength(255),

                        Select::make('type')
                            ->label('Type')
                            ->options(TypeIntervention::class)
                            ->default(TypeIntervention::Curatif)
                            ->required()
                            ->native(false),

                        Select::make('priorite')
                            ->label('Priorité')
                            ->options(PrioriteIntervention::class)
                            ->default(PrioriteIntervention::Normale)
                            ->required()
                            ->native(false),

                        Select::make('statut')
                            ->label('Statut')
                            ->options(StatutIntervention::class)
                            ->default(StatutIntervention::Ouverte)
                            ->required()
                            ->native(false),

                        // Seuls les utilisateurs ayant le rôle Technicien sont proposés.
                        Select::make('technicien_id')
                            ->label('Technicien assigné')
                            ->relationship(
                                'technicien',
                                'name',
                                fn (Builder $query) => $query->whereHas(
                                    'roles',
                                    fn (Builder $q) => $q->where('name', 'Technicien')
                                ),
                            )
                            ->searchable()
                            ->preload()
                            ->placeholder('Non assigné'),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Planification & suivi')
                    ->columns(2)
                    ->schema([
                        DateTimePicker::make('date_demande')
                            ->label('Date de demande')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->default(now()),

                        DateTimePicker::make('date_planifiee')
                            ->label('Date planifiée')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i'),

                        DateTimePicker::make('date_debut')
                            ->label('Début')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i'),

                        DateTimePicker::make('date_fin')
                            ->label('Fin')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i'),

                        TextInput::make('cout')
                            ->label('Coût')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('MAD'),

                        Textarea::make('rapport')
                            ->label('Compte-rendu')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
