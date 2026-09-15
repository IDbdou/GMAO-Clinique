<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'service_id', 'actif'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'actif' => 'boolean',
        ];
    }

    /**
     * Détermine qui peut accéder à chaque panneau Filament.
     *
     * - Panneau /admin  : comptes actifs avec le rôle Admin ou Technicien.
     * - Panneau /agent  : comptes actifs avec le rôle Agent (interface simplifiée).
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if (! $this->actif) {
            return false;
        }

        return match ($panel->getId()) {
            'service' => $this->hasRole('Chef de service'),
            default => $this->hasAnyRole(['Admin', 'Technicien']),
        };
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function interventions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Intervention::class, 'technicien_id');
    }
}
