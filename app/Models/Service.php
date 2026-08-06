<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nom', 'code', 'localisation', 'chef_id', 'adjoint_id', 'actif'])]
class Service extends Model
{
    public function chef(): BelongsTo
    {
        return $this->belongsTo(User::class, 'chef_id');
    }

    public function adjoint(): BelongsTo
    {
        return $this->belongsTo(User::class, 'adjoint_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function equipements(): HasMany
    {
        return $this->hasMany(Equipement::class);
    }

    public function interventions(): HasMany
    {
        return $this->hasMany(Intervention::class);
    }
}
