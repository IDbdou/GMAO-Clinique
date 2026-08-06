<?php

namespace App\Filament\Service\Resources\Signalements\Pages;

use App\Filament\Service\Resources\Signalements\SignalementResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewSignalement extends ViewRecord
{
    protected static string $resource = SignalementResource::class;

    protected static ?string $title = 'Détail du signalement';

    public function infolist(Schema $schema): Schema
    {
        return \App\Filament\Agent\Resources\Signalements\Schemas\SignalementInfolist::configure($schema);
    }
}
