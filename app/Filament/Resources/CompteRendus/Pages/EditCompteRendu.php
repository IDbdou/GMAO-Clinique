<?php

namespace App\Filament\Resources\CompteRendus\Pages;

use App\Filament\Resources\CompteRendus\CompteRenduResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditCompteRendu extends EditRecord
{
    protected static string $resource = CompteRenduResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Si le compte-rendu était en brouillon, on le soumet.
        if ($record->statut === \App\Enums\StatutCompteRendu::Brouillon) {
            $data['statut'] = \App\Enums\StatutCompteRendu::Soumis;
            $data['date_soumission'] = now();

            $record->intervention->update([
                'statut' => \App\Enums\StatutIntervention::EnAttente,
            ]);
        }

        $record->update($data);

        return $record;
    }
}
