<?php

namespace App\Filament\Resources\CompteRendus\Pages;

use App\Filament\Resources\CompteRendus\CompteRenduResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompteRendus extends ListRecords
{
    protected static string $resource = CompteRenduResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
