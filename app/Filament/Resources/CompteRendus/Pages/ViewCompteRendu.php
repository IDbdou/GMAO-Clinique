<?php

namespace App\Filament\Resources\CompteRendus\Pages;

use App\Filament\Resources\CompteRendus\CompteRenduResource;
use Filament\Actions\Action;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewCompteRendu extends ViewRecord
{
    protected static string $resource = CompteRenduResource::class;

    protected static ?string $title = 'Détail du compte-rendu';

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

                Section::make('Signatures')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('signature_technicien')->label('Technicien'),
                        TextEntry::make('date_soumission')->label('Date soumission')->dateTime('d/m/Y H:i'),
                        TextEntry::make('signature_chef_service')->label('Chef de service')->placeholder('—'),
                        TextEntry::make('date_validation')->label('Date validation')->dateTime('d/m/Y H:i')->placeholder('—'),
                    ]),

                Section::make('Validation')
                    ->schema([
                        TextEntry::make('statut')->label('Statut')->badge(),
                        TextEntry::make('commentaire_validation')->label('Commentaire chef de service')->placeholder('—')->columnSpanFull(),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download')
                ->label('Télécharger PDF')
                ->icon('heroicon-m-arrow-down-tray')
                ->color('primary')
                ->url(fn () => '#')
                ->openUrlInNewTab()
                ->visible(false),
        ];
    }
}
