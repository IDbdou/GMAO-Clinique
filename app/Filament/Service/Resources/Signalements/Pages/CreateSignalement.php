<?php

namespace App\Filament\Service\Resources\Signalements\Pages;

use App\Enums\StatutIntervention;
use App\Enums\TypeIntervention;
use App\Filament\Service\Resources\Signalements\SignalementResource;
use App\Models\Equipement;
use Filament\Resources\Pages\CreateRecord;

class CreateSignalement extends CreateRecord
{
    protected static string $resource = SignalementResource::class;

    protected static ?string $title = 'Signaler une panne';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = TypeIntervention::Curatif;
        $data['statut'] = StatutIntervention::Nouveau;
        $data['demandeur_id'] = auth()->id();
        $data['date_demande'] = now();

        $equipement = Equipement::find($data['equipement_id'] ?? null);
        $data['service_id'] = $equipement?->service_id ?? auth()->user()?->service_id;
        $data['titre'] = 'Panne signalée : ' . ($equipement?->nom ?? 'équipement');

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
