<?php

namespace App\Filament\Widgets;

use App\Enums\StatutEquipement;
use App\Models\Equipement;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class EquipementsEnPanne extends BaseWidget
{
    protected static ?string $heading = 'Équipements en panne';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Equipement::query()
                    ->where('statut', StatutEquipement::EnPanne)
                    ->latest()
                    ->limit(5)
            )
            ->defaultPaginationPageOption(5)
            ->paginated(false)
            ->columns([
                TextColumn::make('code_inventaire')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable(),

                TextColumn::make('service.nom')
                    ->label('Service')
                    ->placeholder('—'),

                TextColumn::make('criticite')
                    ->label('Criticité')
                    ->badge()
                    ->sortable(),

                TextColumn::make('localisation')
                    ->label('Localisation')
                    ->placeholder('—'),
            ]);
    }
}
