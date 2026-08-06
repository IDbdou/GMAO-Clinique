<?php

namespace App\Filament\Resources\CompteRendus;

use App\Filament\Resources\CompteRendus\Pages\CreateCompteRendu;
use App\Filament\Resources\CompteRendus\Pages\EditCompteRendu;
use App\Filament\Resources\CompteRendus\Pages\ListCompteRendus;
use App\Filament\Resources\CompteRendus\Pages\ViewCompteRendu;
use App\Filament\Resources\CompteRendus\Schemas\CompteRenduForm;
use App\Filament\Resources\CompteRendus\Tables\CompteRendusTable;
use App\Models\CompteRendu;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CompteRenduResource extends Resource
{
    protected static ?string $model = CompteRendu::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $recordTitleAttribute = 'intervention.titre';

    protected static ?string $navigationLabel = 'Comptes-rendus';

    protected static ?string $modelLabel = 'Compte-rendu';

    protected static ?string $pluralModelLabel = 'Comptes-rendus';

    protected static string|\UnitEnum|null $navigationGroup = 'Maintenance';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return CompteRenduForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompteRendusTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCompteRendus::route('/'),
            'create' => CreateCompteRendu::route('/create'),
            'edit' => EditCompteRendu::route('/{record}/edit'),
            'view' => ViewCompteRendu::route('/{record}'),
        ];
    }
}
