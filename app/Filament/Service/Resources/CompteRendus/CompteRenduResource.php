<?php

namespace App\Filament\Service\Resources\CompteRendus;

use App\Enums\StatutCompteRendu;
use App\Filament\Service\Resources\CompteRendus\Pages\ListCompteRendus;
use App\Filament\Service\Resources\CompteRendus\Pages\ValidateCompteRendu;
use App\Models\CompteRendu;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CompteRenduResource extends Resource
{
    protected static ?string $model = CompteRendu::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $navigationLabel = 'Comptes-rendus à valider';

    protected static ?string $modelLabel = 'compte-rendu';

    protected static ?string $pluralModelLabel = 'comptes-rendus';

    protected static ?string $recordTitleAttribute = 'intervention.titre';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('statut', StatutCompteRendu::Soumis)
            ->whereHas(
                'intervention',
                fn (Builder $query) => $query->where('service_id', auth()->user()?->service_id)
            );
    }

    public static function canCreate(): bool
    {
        return false;
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
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return \App\Filament\Service\Resources\CompteRendus\Tables\CompteRendusTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCompteRendus::route('/'),
            'view' => ValidateCompteRendu::route('/{record}'),
        ];
    }
}
