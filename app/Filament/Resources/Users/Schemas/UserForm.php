<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nom')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                TextInput::make('password')
                    ->label('Mot de passe')
                    ->password()
                    ->revealable()
                    // Obligatoire à la création, optionnel en modification (laisser vide = inchangé).
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->maxLength(255)
                    ->helperText('En modification, laisser vide pour conserver le mot de passe actuel.'),

                // Rôle unique — synchronisé avec Spatie via les pages Create/Edit.
                Select::make('role')
                    ->label('Rôle')
                    ->options([
                        'Admin' => 'Admin',
                        'Technicien' => 'Technicien',
                        'Agent' => 'Agent',
                    ])
                    ->required()
                    ->native(false),

                TextInput::make('service')
                    ->label('Service')
                    ->maxLength(255)
                    ->placeholder('Ex : Radiologie, Hémodialyse, Bloc opératoire…')
                    ->helperText('Service d’affectation (facultatif — laisser vide pour un Admin).'),

                Toggle::make('actif')
                    ->label('Actif')
                    ->default(true)
                    ->helperText('Décocher pour désactiver l’accès sans supprimer le compte.'),
            ]);
    }
}
