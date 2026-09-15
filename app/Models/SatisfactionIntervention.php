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
}
