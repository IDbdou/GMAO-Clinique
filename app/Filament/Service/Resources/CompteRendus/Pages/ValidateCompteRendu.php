<?php

namespace App\Filament\Service\Resources\CompteRendus\Pages;

use App\Enums\StatutCompteRendu;
use App\Enums\StatutIntervention;
use App\Filament\Service\Resources\CompteRendus\CompteRenduResource;
use App\Models\CompteRendu;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ValidateCompteRendu extends ViewRecord
{
    protected static string $resource = CompteRenduResource::class;

    protected static ?string $title = 'Validation du compte-rendu';

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(fn (CompteRendu $record): string => "Compte-rendu n° CR-{$record->id} / Intervention : {$record->intervention?->titre}")
                    ->description(fn (CompteRendu $record): string => 'Soumis le ' . ($record->date_soumission?->format('d/m/Y H:i') ?? '—'))
                    ->icon('heroicon-m-clipboard-document-check')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Group::make([
                                    TextEntry::make('intervention.service.nom')
                                        ->label('Service demandeur')
                                        ->placeholder('—'),
                                    TextEntry::make('intervention.equipement.nom')
                                        ->label('Équipement concerné')
                                        ->placeholder('—'),
                                    TextEntry::make('intervention.equipement.numero_serie')
                                        ->label('N° série')
                                        ->placeholder('—'),
                                ]),
                                Group::make([
                                    TextEntry::make('technicien.name')
                                        ->label('Technicien intervenant')
                                        ->placeholder('—'),
                                    TextEntry::make('intervention.date_demande')
                                        ->label('Date de la demande')
                                        ->dateTime('d/m/Y H:i')
                                        ->placeholder('—'),
                                    TextEntry::make('intervention.date_fin')
                                        ->label('Date de clôture')
                                        ->dateTime('d/m/Y H:i')
                                        ->placeholder('—'),
                                ]),
                            ]),
                    ]),

                Section::make('Rapport technique')
                    ->icon('heroicon-m-wrench-screwdriver')
                    ->schema([
                        TextEntry::make('observations')
                            ->label('Observations / travaux réalisés')
                            ->placeholder('Aucune observation')
                            ->columnSpanFull(),
                        TextEntry::make('pieces_utilisees')
                            ->label('Pièces et consommables utilisés')
                            ->placeholder('Aucune pièce')
                            ->columnSpanFull(),
                    ]),

                Section::make('Détail des coûts')
                    ->icon('heroicon-m-currency-dirham')
                    ->columns(4)
                    ->schema([
                        TextEntry::make('temps_passe')
                            ->label('Temps passé')
                            ->suffix(' h')
                            ->placeholder('—'),
                        TextEntry::make('cout_main_oeuvre')
                            ->label('Main d\'œuvre')
                            ->money('MAD')
                            ->placeholder('—'),
                        TextEntry::make('cout_pieces')
                            ->label('Pièces / consommables')
                            ->money('MAD')
                            ->placeholder('—'),
                        TextEntry::make('cout_total')
                            ->label('Coût total')
                            ->money('MAD')
                            ->placeholder('—')
                            ->weight('bold'),
                    ]),

                Section::make('Signature du technicien')
                    ->icon('heroicon-m-pencil-square')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('signature_technicien')
                            ->label('Signataire')
                            ->placeholder('—'),
                        TextEntry::make('date_soumission')
                            ->label('Date de soumission')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('—'),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();

        if ($record->statut !== StatutCompteRendu::Soumis) {
            return [
                Action::make('print')
                    ->label('Imprimer / PDF')
                    ->icon('heroicon-m-printer')
                    ->extraAttributes(['onclick' => 'window.print()']),
            ];
        }

        return [
            Action::make('valider')
                ->label('✅ Valider le compte-rendu')
                ->icon('heroicon-m-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Validation du compte-rendu')
                ->modalDescription('En validant, l\'intervention sera clôturée et le compte-rendu archivé.')
                ->form([
                    Textarea::make('commentaire')
                        ->label('Commentaire de validation (optionnel)')
                        ->rows(3),
                ])
                ->action(function (array $data): void {
                    $record = $this->getRecord();

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

                    $this->redirect($this->getResource()::getUrl('index'));
                }),

            Action::make('refuser')
                ->label('❌ Refuser')
                ->icon('heroicon-m-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Refus du compte-rendu')
                ->modalDescription('Le technicien devra soumettre une nouvelle version.')
                ->form([
                    Textarea::make('commentaire')
                        ->label('Motif du refus')
                        ->required()
                        ->rows(3),
                ])
                ->action(function (array $data): void {
                    $record = $this->getRecord();

                    $record->update([
                        'statut' => StatutCompteRendu::Refuse,
                        'commentaire_validation' => $data['commentaire'],
                    ]);

                    $this->redirect($this->getResource()::getUrl('index'));
                }),
        ];
    }
}
