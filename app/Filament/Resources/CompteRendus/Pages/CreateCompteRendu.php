<?php

namespace App\Filament\Resources\CompteRendus\Pages;

use App\Filament\Resources\CompteRendus\CompteRenduResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateCompteRendu extends CreateRecord
{
    protected static string $resource = CompteRenduResource::class;

    protected static ?string $title = 'Rédiger un compte-rendu';

    public function mount(): void
    {
        parent::mount();

        // Préremplir l'intervention depuis l'URL si fournie.
        $interventionId = request('intervention');
        if ($interventionId) {
            $this->form->fill([
                'intervention_id' => $interventionId,
                'technicien_id' => auth()->id(),
                'statut' => \App\Enums\StatutCompteRendu::Brouillon->value,
                'signature_technicien' => auth()->user()?->name,
                'date_soumission' => now(),
            ]);
        }
    }

    protected function handleRecordCreation(array $data): Model
    {
        $data['statut'] = \App\Enums\StatutCompteRendu::Soumis;
        $data['date_soumission'] = now();

        /** @var \App\Models\CompteRendu $compteRendu */
        $compteRendu = static::getModel()::create($data);

        // Mettre à jour l'intervention liée : passée en attente de validation.
        $compteRendu->intervention->update([
            'statut' => \App\Enums\StatutIntervention::EnAttente,
        ]);

        return $compteRendu;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
