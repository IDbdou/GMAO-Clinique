<?php

namespace App\Filament\Widgets;

use App\Enums\StatutIntervention;
use App\Models\Intervention;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class DerniersSignalements extends BaseWidget
{
    protected static ?string $heading = 'Derniers signalements';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Intervention::query()
                    ->where('type', 'curatif')
                    ->whereIn('statut', [
                        StatutIntervention::Nouveau,
                        StatutIntervention::Ouverte,
                        StatutIntervention::EnCours,
                        StatutIntervention::EnAttente,
                    ])
                    ->latest('date_demande')
                    ->limit(5)
            )
            ->defaultPaginationPageOption(5)
            ->paginated(false)
            ->columns([
                TextColumn::make('titre')
                    ->label('Titre')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('equipement.nom')
                    ->label('Équipement')
                    ->searchable(),

                TextColumn::make('priorite')
                    ->label('Priorité')
                    ->badge()
                    ->sortable(),

                TextColumn::make('statut')
                    ->label('Statut')
                    ->badge()
                    ->sortable(),

                TextColumn::make('technicien.name')
                    ->label('Technicien')
                    ->placeholder('Non assigné')
                    ->toggleable(),

                TextColumn::make('date_demande')
                    ->label('Signalé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ]);
    }
}
