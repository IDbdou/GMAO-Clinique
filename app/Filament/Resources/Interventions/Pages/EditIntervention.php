<?php

namespace App\Filament\Resources\Interventions\Pages;

use App\Filament\Resources\Interventions\InterventionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditIntervention extends EditRecord
{
    protected static string $resource = InterventionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Si une date de fin est saisie, forcer le statut à Terminée.
        if (filled($data['date_fin'] ?? null) && $data['statut'] !== \App\Enums\StatutIntervention::Terminee->value) {
            $data['statut'] = \App\Enums\StatutIntervention::Terminee->value;
        }

        return $data;
    }
}
