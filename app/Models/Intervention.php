<?php

namespace App\Models;

use App\Enums\PrioriteIntervention;
use App\Enums\StatutIntervention;
use App\Enums\TypeIntervention;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'equipement_id', 'service_id', 'technicien_id', 'demandeur_id', 'titre', 'type', 'priorite', 'statut',
    'description', 'rapport', 'date_demande', 'date_planifiee',
    'date_debut', 'date_fin', 'cout',
])]
class Intervention extends Model
{
    protected function casts(): array
    {
        return [
            'type' => TypeIntervention::class,
            'priorite' => PrioriteIntervention::class,
            'statut' => StatutIntervention::class,
            'date_demande' => 'datetime',
            'date_planifiee' => 'datetime',
            'date_debut' => 'datetime',
            'date_fin' => 'datetime',
            'cout' => 'decimal:2',
        ];
    }

    public function equipement(): BelongsTo
    {
        return $this->belongsTo(Equipement::class);
    }

    public function technicien(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technicien_id');
    }

    public function demandeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'demandeur_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function compteRendu(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CompteRendu::class);
    }

    public function satisfaction(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(SatisfactionIntervention::class);
    }
}
