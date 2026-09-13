<?php

namespace App\Filament\Widgets;

use App\Models\ContratMaintenance;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ContratsAlertes extends BaseWidget
{
    protected static ?string $heading = 'Contrats de maintenance à surveiller';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 5;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ContratMaintenance::query()
                    ->with('equipement')
                    ->where(function ($q) {
                        $q->where('actif', true)
                            ->where('date_fin', '<=', now()->addMonths(3)->toDateString());
                    })
                    ->orWhere(function ($q) {
                        $q->where('actif', false)
                            ->where('date_fin', '<', now()->toDateString());
                    })
                    ->orderBy('date_fin')
                    ->limit(8)
            )
            ->defaultPaginationPageOption(8)
            ->paginated(false)
            ->columns([
                TextColumn::make('equipement.nom')
                    ->label('Équipement')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('reference')
                    ->label('N° contrat')
                    ->searchable()
                    ->fontFamily('mono')
                    ->size('sm'),

                TextColumn::make('type_contrat')
                    ->label('Type')
                    ->badge()
                    ->sortable(),

                TextColumn::make('fournisseur')
                    ->label('Fournisseur')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('date_fin')
                    ->label('Fin garantie')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record) => $record->estExpire() ? 'danger' : ($record->estEnAlerte() ? 'warning' : 'gray'))
                    ->icon(fn ($record) => $record->estExpire() ? 'heroicon-m-x-circle' : ($record->estEnAlerte() ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')),

                TextColumn::make('cout_annuel')
                    ->label('Coût annuel')
                    ->money('MAD')
                    ->sortable(),

                TextColumn::make('joursRestants')
                    ->label('Jours restants')
                    ->state(function ($record) {
                        $jours = $record->joursRestants();
                        if ($jours === 0) return 'EXPIRÉ';
                        return $jours . ' j';
                    })
                    ->color(fn ($record) => $record->estExpire() ? 'danger' : ($record->estEnAlerte() ? 'warning' : 'success'))
                    ->badge(),
            ]);
    }
}
