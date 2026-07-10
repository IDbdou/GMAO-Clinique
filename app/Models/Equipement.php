<?php

namespace App\Models;

use App\Enums\Criticite;
use App\Enums\StatutEquipement;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'nom', 'code_inventaire', 'numero_serie', 'marque', 'modele',
    'service', 'localisation', 'criticite', 'statut',
    'date_mise_en_service', 'fournisseur', 'notes',
])]
class Equipement extends Model
{
    protected function casts(): array
    {
        return [
            'criticite' => Criticite::class,
            'statut' => StatutEquipement::class,
            'date_mise_en_service' => 'date',
        ];
    }

    public function interventions(): HasMany
    {
        return $this->hasMany(Intervention::class);
    }
}
