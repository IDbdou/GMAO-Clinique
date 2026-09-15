<?php

namespace App\Filament\Service\Resources\Signalements;

use App\Enums\PrioriteIntervention;
use App\Filament\Service\Resources\Signalements\Pages\CreateSignalement;
use App\Filament\Service\Resources\Signalements\Pages\ListSignalements;
use App\Filament\Service\Resources\Signalements\Pages\ViewSignalement;
use App\Models\Intervention;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SignalementResource extends Resource
{
    protected static ?string $model = Intervention::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static ?string $navigationLabel = 'Signalements de mon service';

    protected static ?string $modelLabel = 'signalement';

    protected static ?string $pluralModelLabel = 'signalements';

    protected static ?string $recordTitleAttribute = 'titre';

    /**
     * Le Chef de service ne voit QUE les signalements/interventions de son propre service.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('service_id', auth()->user()?->service_id);
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('equipement_id')
                    ->label('Équipement concerné')
                    ->relationship(
                        'equipement',
                        'nom',
                        fn (Builder $query) => $query->where('service_id', auth()->user()?->service_id)
                    )
                    ->searchable()
                    ->preload()
                    ->required(),

                \Filament\Forms\Components\Select::make('priorite')
                    ->label('Niveau d’urgence')
                    ->options(PrioriteIntervention::class)
                    ->default(PrioriteIntervention::Normale)
                    ->required()
                    ->native(false),

                \Filament\Forms\Components\Textarea::make('description')
                    ->label('Description de la panne')
                    ->placeholder('Décrivez le problème constaté…')
                    ->rows(5)
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return \App\Filament\Agent\Resources\Signalements\Tables\SignalementsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSignalements::route('/'),
            'create' => CreateSignalement::route('/create'),
            'view' => ViewSignalement::route('/{record}'),
        ];
    }
}
