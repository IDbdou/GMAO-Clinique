<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'intervention_id', 'evaluateur_id', 'note', 'commentaire',
])]
class SatisfactionIntervention extends Model
{
    protected $table = 'satisfactions_intervention';

    protected function casts(): array
    {
        return [
            'note' => 'integer',
        ];
    }

    public function intervention(): BelongsTo
    {
        return $this->belongsTo(Intervention::class);
    }

    public function evaluateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluateur_id');
    }

    /**
     * Retourne la note sous forme d'étoiles.
     */
    public function etoiles(): string
    {
        return str_repeat('★', $this->note) . str_repeat('☆', 5 - $this->note);
    }

    /**
     * Couleur en fonction de la note.
     */
    public function couleur(): string
    {
        return match ($this->note) {
            5 => 'success',
            4 => 'info',
            3 => 'warning',
            default => 'danger',
        };
    }
}
