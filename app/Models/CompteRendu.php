<?php

namespace App\Models;

use App\Enums\StatutCompteRendu;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'intervention_id', 'technicien_id', 'observations', 'pieces_utilisees', 'temps_passe',
    'cout_main_oeuvre', 'cout_pieces', 'cout_total', 'statut',
    'date_soumission', 'date_validation', 'commentaire_validation',
    'signature_technicien', 'signature_chef_service',
])]
class CompteRendu extends Model
{
    protected function casts(): array
    {
        return [
            'statut' => StatutCompteRendu::class,
            'date_soumission' => 'datetime',
            'date_validation' => 'datetime',
            'temps_passe' => 'decimal:2',
            'cout_main_oeuvre' => 'decimal:2',
            'cout_pieces' => 'decimal:2',
            'cout_total' => 'decimal:2',
        ];
    }

    public function intervention(): BelongsTo
    {
        return $this->belongsTo(Intervention::class);
    }

    public function technicien(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technicien_id');
    }
}
