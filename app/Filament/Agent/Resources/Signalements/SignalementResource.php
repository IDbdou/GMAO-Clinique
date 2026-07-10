<?php

namespace App\Filament\Agent\Resources\Signalements;

use App\Filament\Agent\Resources\Signalements\Pages\CreateSignalement;
use App\Filament\Agent\Resources\Signalements\Pages\ListSignalements;
use App\Filament\Agent\Resources\Signalements\Pages\ViewSignalement;
use App\Filament\Agent\Resources\Signalements\Schemas\SignalementForm;
use App\Filament\Agent\Resources\Signalements\Schemas\SignalementInfolist;
use App\Filament\Agent\Resources\Signalements\Tables\SignalementsTable;
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

    protected static ?string $navigationLabel = 'Mes signalements';

    protected static ?string $modelLabel = 'signalement';

    protected static ?string $pluralModelLabel = 'signalements';

    protected static ?string $recordTitleAttribute = 'titre';

    /**
     * Un Agent ne voit QUE ses propres signalements.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('demandeur_id', auth()->id());
    }

    // Un signalement, une fois soumis, n'est plus modifiable ni supprimable par l'Agent.
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
        return SignalementForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SignalementInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SignalementsTable::configure($table);
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
