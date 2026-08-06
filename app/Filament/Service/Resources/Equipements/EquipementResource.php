<?php

namespace App\Filament\Service\Resources\Equipements;

use App\Models\Equipement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EquipementResource extends Resource
{
    protected static ?string $model = Equipement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCpuChip;

    protected static ?string $navigationLabel = 'Équipements de mon service';

    protected static ?string $modelLabel = 'Équipement';

    protected static ?string $pluralModelLabel = 'Équipements';

    protected static ?string $recordTitleAttribute = 'nom';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('service_id', auth()->user()?->service_id);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return \App\Filament\Resources\Equipements\Tables\EquipementsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Service\Resources\Equipements\Pages\ListEquipements::route('/'),
        ];
    }
}
