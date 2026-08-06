<?php

namespace App\Filament\Service\Resources\CompteRendus\Pages;

use App\Enums\StatutCompteRendu;
use App\Enums\StatutIntervention;
use App\Filament\Service\Resources\CompteRendus\CompteRenduResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ValidateCompteRendu extends ViewRecord
{
    protected static string $resource = CompteRenduResource::class;

    protected static ?string $title = 'Valider le compte-rendu';

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Intervention')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('intervention.titre')->label('Titre'),
                        TextEntry::make('intervention.equipement.nom')->label('Équipement'),
                        TextEntry::make('intervention.service.nom')->label('Service'),
                        TextEntry::make('technicien.name')->label('Technicien'),
                    ]),

                Section::make('Compte-rendu technique')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('observations')->label('Observations')->columnSpanFull(),
                        TextEntry::make('pieces_utilisees')->label('Pièces utilisées')->columnSpanFull(),
                        TextEntry::make('temps_passe')->label('Temps passé')->suffix(' h'),
                        TextEntry::make('cout_total')->label('Coût total')->money('MAD'),
                    ]),

                Section::make('Signature technicien')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('signature_technicien')->label('Nom'),
                        TextEntry::make('date_soumission')->label('Date soumission')->dateTime('d/m/Y H:i'),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('valider')
                ->label('✅ Valider le compte-rendu')
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
