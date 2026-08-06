<?php

namespace App\Filament\Service\Resources\Signalements\Pages;

use App\Filament\Service\Resources\Signalements\SignalementResource;
use Filament\Resources\Pages\ListRecords;

class ListSignalements extends ListRecords
{
    protected static string $resource = SignalementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
