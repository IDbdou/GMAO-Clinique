<?php

namespace App\Filament\Agent\Resources\Signalements\Pages;

use App\Enums\StatutIntervention;
use App\Enums\TypeIntervention;
use App\Filament\Agent\Resources\Signalements\SignalementResource;
use App\Models\Equipement;
use Filament\Resources\Pages\CreateRecord;

class CreateSignalement extends CreateRecord
{
    protected static string $resource = SignalementResource::class;

    protected static ?string $title = 'Signaler une panne';

    /**
     * Complète le signalement de l'Agent pour en faire une intervention corrective :
     * type Curatif, statut Nouveau, demandeur = l'Agent connecté, titre auto.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = TypeIntervention::Curatif;
        $data['statut'] = StatutIntervention::Nouveau;
        $data['demandeur_id'] = auth()->id();
        $data['date_demande'] = now();

        $equipement = Equipement::find($data['equipement_id'] ?? null);
        $data['titre'] = 'Panne signalée : ' . ($equipement?->nom ?? 'équipement');

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
