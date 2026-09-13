<?php

namespace App\Filament\Widgets;

use App\Models\SatisfactionIntervention;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class SatisfactionTechniciens extends BaseWidget
{
    protected static ?string $heading = 'Évaluation des techniciens (satisfaction)';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 6;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->whereHas('roles', fn ($q) => $q->where('name', 'Technicien'))
                    ->withCount([
                        'interventions as total_interventions' => fn ($q) => $q->where('statut', 'terminee'),
                        'interventions as evaluations_count' => fn (Builder $q) => $q->whereHas('satisfaction'),
                    ])
                    ->addSelect([
                        'moyenne' => SatisfactionIntervention::query()
                            ->selectRaw('AVG(satisfactions_intervention.note)')
                            ->join('interventions', 'interventions.id', '=', 'satisfactions_intervention.intervention_id')
                            ->whereColumn('interventions.technicien_id', 'users.id'),
                    ])
                    ->orderByDesc('moyenne')
            )
            ->paginated(false)
            ->columns([
                TextColumn::make('name')
                    ->label('Technicien')
                    ->searchable(),

                TextColumn::make('total_interventions')
                    ->label('Interventions')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('evaluations_count')
                    ->label('Évaluations')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('moyenne')
                    ->label('Moyenne')
                    ->alignCenter()
                    ->numeric(1)
                    ->sortable()
                    ->color(fn ($record) => match(true) {
                        ($record->moyenne ?? 0) >= 4.5 => 'success',
                        ($record->moyenne ?? 0) >= 3.5 => 'info',
                        ($record->moyenne ?? 0) >= 2.5 => 'warning',
                        default => 'danger',
                    })
                    ->prefix(fn ($record) => $record->moyenne ? str_repeat('★', round($record->moyenne)) . ' ' : '')
                    ->placeholder('Aucune'),

                TextColumn::make('service.nom')
                    ->label('Service affecté')
                    ->placeholder('—'),
            ]);
    }
}
