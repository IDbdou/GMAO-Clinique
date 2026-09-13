<?php

namespace App\Models;

use App\Enums\FrequencePreventive;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'equipement_id', 'titre', 'description', 'frequence',
    'delai_alerte_jours', 'derniere_date', 'prochaine_date', 'actif',
])]
class PlanningPreventif extends Model
{
    protected $table = 'plannings_preventifs';

    protected function casts(): array
    {
        return [
            'frequence' => FrequencePreventive::class,
            'derniere_date' => 'date',
            'prochaine_date' => 'date',
            'actif' => 'boolean',
        ];
    }

    public function equipement(): BelongsTo
    {
        return $this->belongsTo(Equipement::class);
    }

    /**
     * Met à jour la prochaine date après une maintenance effectuée.
     */
    public function reporterProchaineDate(): void
    {
        $this->update([
            'derniere_date' => now()->toDateString(),
            'prochaine_date' => $this->frequence->prochaineDate(now()),
        ]);
    }

    /**
     * Vérifie si la maintenance est en retard.
     */
    public function estEnRetard(): bool
    {
        return $this->prochaine_date < now()->toDateString();
    }

    /**
     * Vérifie si une alerte doit être affichée (proche échéance).
     */
    public function estEnAlerte(): bool
    {
        if ($this->estEnRetard()) return true;

        $dateAlerte = now()->addDays($this->delai_alerte_jours)->toDateString();
        return $this->prochaine_date <= $dateAlerte;
    }
}
