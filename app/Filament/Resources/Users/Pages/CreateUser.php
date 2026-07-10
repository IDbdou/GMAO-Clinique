<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $role = $data['role'] ?? null;
        unset($data['role']); // 'role' n'est pas une colonne : géré via Spatie

        /** @var \App\Models\User $user */
        $user = static::getModel()::create($data);

        if ($role) {
            $user->syncRoles([$role]);
        }

        return $user;
    }
}
