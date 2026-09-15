<?php

namespace App\Filament\Service\Resources\CompteRendus\Tables;

use App\Enums\StatutCompteRendu;
use App\Enums\StatutIntervention;
use App\Models\CompteRendu;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CompteRendusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('date_soumission', 'desc')
            ->columns([
                TextColumn::make('intervention.titre')
                    ->label('Intervention')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('intervention.equipement.nom')
                    ->label('Équipement')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('technicien.name')
                    ->label('Technicien')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('statut')
                    ->label('Statut')
                    ->badge()
                    ->sortable(),

                TextColumn::make('temps_passe')
                    ->label('Temps')
                    ->suffix(' h')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('cout_total')
                    ->label('Coût total')
                    ->money('MAD')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('date_soumission')
                    ->label('Soumis le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('statut')
                    ->label('Statut')
                    ->options(StatutCompteRendu::class),
            ])
            ->recordActions([
                ViewAction::make(),

                Action::make('valider')
                    ->label('Valider')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->visible(fn (CompteRendu $record): bool => $record->statut === StatutCompteRendu::Soumis)
                    ->requiresConfirmation()
                    ->modalHeading('Valider le compte-rendu')
                    ->modalDescription('L\'intervention sera clôturée et le compte-rendu archivé.')
                    ->form([
                        Textarea::make('commentaire')
                            ->label('Commentaire de validation (optionnel)')
                            ->rows(3),
                    ])
                    ->action(function (CompteRendu $record, array $data): void {
                        $record->update([
                            'statut' => StatutCompteRendu::Valide,
                            'date_validation' => now(),
                            'signature_chef_service' => auth()->user()?->name,
                            'commentaire_validation' => $data['commentaire'] ?? null,
                        ]);

                        $record->intervention->update([
                            'statut' => StatutIntervention::Terminee,
                            'date_fin' => now(),
                        ]);
                    }),

                Action::make('refuser')
                    ->label('Refuser')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->visible(fn (CompteRendu $record): bool => $record->statut === StatutCompteRendu::Soumis)
                    ->requiresConfirmation()
                    ->modalHeading('Refuser le compte-rendu')
                    ->modalDescription('Le technicien devra soumettre une nouvelle version.')
                    ->form([
                        Textarea::make('commentaire')
                            ->label('Motif du refus')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (CompteRendu $record, array $data): void {
                        $record->update([
                            'statut' => StatutCompteRendu::Refuse,
                            'commentaire_validation' => $data['commentaire'],
                        ]);
                    }),
            ]);
    }
}
