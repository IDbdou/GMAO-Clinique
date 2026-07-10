<?php

namespace App\Filament\Resources\Equipements;

use App\Filament\Resources\Equipements\Pages\CreateEquipement;
use App\Filament\Resources\Equipements\Pages\EditEquipement;
use App\Filament\Resources\Equipements\Pages\ListEquipements;
use App\Filament\Resources\Equipements\Schemas\EquipementForm;
use App\Filament\Resources\Equipements\Tables\EquipementsTable;
use App\Models\Equipement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EquipementResource extends Resource
{
    protected static ?string $model = Equipement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCpuChip;

    protected static ?string $recordTitleAttribute = 'nom';

    protected static ?string $navigationLabel = 'Équipements';

    protected static ?string $modelLabel = 'Équipement';

    protected static ?string $pluralModelLabel = 'Équipements';

    protected static string|\UnitEnum|null $navigationGroup = 'Maintenance';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return EquipementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EquipementsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEquipements::route('/'),
            'create' => CreateEquipement::route('/create'),
            'edit' => EditEquipement::route('/{record}/edit'),
        ];
    }
}
