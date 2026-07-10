<?php

namespace App\Filament\Agent\Resources\Signalements\Pages;

use App\Filament\Agent\Resources\Signalements\SignalementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSignalements extends ListRecords
{
    protected static string $resource = SignalementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Signaler une panne'),
        ];
    }
}
