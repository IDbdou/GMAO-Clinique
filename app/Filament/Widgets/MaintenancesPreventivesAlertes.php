<?php

namespace App\Filament\Widgets;

use App\Models\PlanningPreventif;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class MaintenancesPreventivesAlertes extends BaseWidget
{
    protected static ?string $heading = 'Maintenances préventives à venir';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PlanningPreventif::query()
                    ->with('equipement')
                    ->where('actif', true)
                    ->where('prochaine_date', '<=', now()->addDays(30)->toDateString())
                    ->orderBy('prochaine_date')
                    ->limit(10)
            )
            ->defaultPaginationPageOption(10)
            ->paginated(false)
            ->columns([
                TextColumn::make('equipement.nom')
                    ->label('Équipement')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('titre')
                    ->label('Maintenance')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('frequence')
                    ->label('Fréquence')
                    ->badge()
                    ->sortable(),

                TextColumn::make('prochaine_date')
                    ->label('Prochaine date')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record) => $record->estEnRetard() ? 'danger' : ($record->estEnAlerte() ? 'warning' : 'gray'))
                    ->icon(fn ($record) => $record->estEnRetard() ? 'heroicon-m-exclamation-circle' : null),

                TextColumn::make('derniere_date')
                    ->label('Dernière maintenance')
                    ->date('d/m/Y')
                    ->placeholder('Jamais')
                    ->sortable(),

                TextColumn::make('equipement.service.nom')
                    ->label('Service')
                    ->placeholder('—'),
            ]);
    }
}
