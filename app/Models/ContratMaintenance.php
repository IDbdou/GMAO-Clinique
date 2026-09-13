<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'equipement_id', 'reference', 'type_contrat', 'fournisseur', 'contact',
    'date_debut', 'date_fin', 'cout_annuel', 'delai_alerte_mois', 'notes', 'actif',
])]
class ContratMaintenance extends Model
{
    protected $table = 'contrats_maintenance';

    protected function casts(): array
    {
        return [
            'date_debut' => 'date',
            'date_fin' => 'date',
            'cout_annuel' => 'decimal:2',
            'actif' => 'boolean',
        ];
    }

    public function equipement(): BelongsTo
    {
        return $this->belongsTo(Equipement::class);
    }

    public function estExpire(): bool
    {
        return $this->date_fin < now()->toDateString();
    }

    public function joursRestants(): int
    {
        return max(0, now()->diffInDays($this->date_fin, false));
    }

    public function estEnAlerte(): bool
    {
        if ($this->estExpire()) return true;

        $dateAlerte = now()->addMonths($this->delai_alerte_mois)->toDateString();
        return $this->date_fin <= $dateAlerte;
    }
}
