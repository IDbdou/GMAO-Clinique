<?php

namespace App\Filament\Resources\Interventions\Tables;

use App\Enums\PrioriteIntervention;
use App\Enums\StatutIntervention;
use App\Enums\TypeIntervention;
use App\Models\Intervention;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InterventionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('date_demande', 'desc')
            ->columns([
                TextColumn::make('titre')
                    ->label('Titre')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('equipement.nom')
                    ->label('Équipement')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('service.nom')
                    ->label('Service')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('priorite')
                    ->label('Priorité')
                    ->badge()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('statut')
                    ->label('Statut')
                    ->badge()
                    ->sortable(),

                TextColumn::make('technicien.name')
                    ->label('Technicien')
                    ->placeholder('Non assigné')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('date_demande')
                    ->label('Demandée le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('cout')
                    ->label('Coût')
                    ->money('MAD')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->options(TypeIntervention::class),

                SelectFilter::make('priorite')
                    ->label('Priorité')
                    ->options(PrioriteIntervention::class),

                SelectFilter::make('statut')
                    ->label('Statut')
                    ->options(StatutIntervention::class),

                SelectFilter::make('technicien')
                    ->label('Technicien')
                    ->relationship('technicien', 'name'),

                SelectFilter::make('equipement')
                    ->label('Équipement')
                    ->relationship('equipement', 'nom')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('service')
                    ->label('Service')
                    ->relationship('service', 'nom'),
            ])
            ->recordActions([
                EditAction::make(),

                // Actions rapides regroupees dans un menu "..." : evite que la
                // colonne d'actions ne deborde sur mobile (5 boutons a plat).
                ActionGroup::make([
                    // Prendre en charge / Ouvrir
                    Action::make('prendreEnCharge')
                        ->label('Prendre en charge')
                        ->icon('heroicon-m-hand-thumb-up')
                        ->color('warning')
                        ->visible(fn (Intervention $record): bool => in_array($record->statut, [StatutIntervention::Nouveau, StatutIntervention::Ouverte]))
                        ->requiresConfirmation()
                        ->modalHeading('Prendre en charge l\'intervention')
                        ->action(function (Intervention $record): void {
                            $record->update([
                                'statut' => StatutIntervention::EnCours,
                                'technicien_id' => auth()->id(),
                                'date_debut' => $record->date_debut ?? now(),
                            ]);
                        }),

                    // Clôturer
                    Action::make('cloturer')
                        ->label('Clôturer')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->visible(fn (Intervention $record): bool => in_array($record->statut, [StatutIntervention::Nouveau, StatutIntervention::Ouverte, StatutIntervention::EnCours, StatutIntervention::EnAttente]))
                        ->requiresConfirmation()
                        ->modalHeading('Clôturer l\'intervention')
                        ->action(function (Intervention $record): void {
                            $record->update([
                                'statut' => StatutIntervention::Terminee,
                                'date_fin' => now(),
                            ]);
                        }),

                    // Annuler
                    Action::make('annuler')
                        ->label('Annuler')
                        ->icon('heroicon-m-x-circle')
                        ->color('danger')
                        ->visible(fn (Intervention $record): bool => ! in_array($record->statut, [StatutIntervention::Terminee, StatutIntervention::Annulee]))
                        ->requiresConfirmation()
                        ->modalHeading('Annuler l\'intervention')
                        ->action(function (Intervention $record): void {
                            $record->update([
                                'statut' => StatutIntervention::Annulee,
                            ]);
                        }),

                    // Compte-rendu (technicien)
                    Action::make('compteRendu')
                        ->label('Compte-rendu')
                        ->icon('heroicon-m-clipboard-document-check')
                        ->color('info')
                        ->visible(fn (Intervention $record): bool => in_array($record->statut, [StatutIntervention::Nouveau, StatutIntervention::Ouverte, StatutIntervention::EnCours, StatutIntervention::EnAttente]))
                        ->url(fn (Intervention $record): string => route('filament.admin.resources.compte-rendus.create', ['intervention' => $record->id])),
                ])
                    ->label('Actions rapides')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->color('gray'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
