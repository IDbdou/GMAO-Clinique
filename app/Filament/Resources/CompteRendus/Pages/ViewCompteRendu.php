<?php

namespace App\Filament\Resources\CompteRendus\Pages;

use App\Filament\Resources\CompteRendus\CompteRenduResource;
use App\Models\CompteRendu;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewCompteRendu extends ViewRecord
{
    protected static string $resource = CompteRenduResource::class;

    protected static ?string $title = 'Compte-rendu d\'intervention';

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(fn (CompteRendu $record): string => "Compte-rendu n° CR-{$record->id} / Intervention : {$record->intervention?->titre}")
                    ->description(fn (CompteRendu $record): string => 'Établi le ' . ($record->date_soumission?->format('d/m/Y H:i') ?? '—'))
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
                    ->icon('heroicon-m-banknotes')
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

                Section::make('Validation du service')
                    ->icon('heroicon-m-shield-check')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('statut')
                            ->label('Statut')
                            ->badge()
                            ->columnSpanFull(),
                        TextEntry::make('signature_chef_service')
                            ->label('Validé / refusé par')
                            ->placeholder('En attente de validation'),
                        TextEntry::make('date_validation')
                            ->label('Date de validation')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('—'),
                        TextEntry::make('commentaire_validation')
                            ->label('Commentaire du chef de service')
                            ->placeholder('—')
                            ->columnSpanFull(),
                    ]),

                Section::make('Signatures')
                    ->icon('heroicon-m-pencil-square')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('signature_technicien')
                            ->label('Signature du technicien')
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
        return [
            Action::make('pdf')
                ->label('Télécharger le PDF')
                ->icon('heroicon-m-arrow-down-tray')
                ->color('primary')
                ->url(fn (): string => route('compte-rendu.pdf', $this->getRecord()))
                ->openUrlInNewTab(),
        ];
    }
}
