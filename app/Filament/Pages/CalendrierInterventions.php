<?php

namespace App\Filament\Pages;

use App\Enums\StatutIntervention;
use App\Enums\TypeIntervention;
use App\Filament\Resources\Interventions\InterventionResource;
use App\Models\Equipement;
use App\Models\User;
use App\Support\Calendar\InterventionColorResolver;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class CalendrierInterventions extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel = 'Calendrier';

    protected static ?string $title = 'Calendrier des interventions';

    protected string $view = 'filament.pages.calendrier-interventions';

    protected static string|\UnitEnum|null $navigationGroup = 'Maintenance';

    protected static ?int $navigationSort = 10;

    public array $statutOptions = [];

    public array $typeOptions = [];

    public array $technicienOptions = [];

    public array $equipementOptions = [];

    public string $eventsUrl = '';

    public string $rescheduleUrlTemplate = '';

    public string $createUrl = '';

    public function mount(): void
    {
        $this->eventsUrl = route('admin.calendrier.events');
        $this->rescheduleUrlTemplate = route('admin.calendrier.reschedule', ['intervention' => '__ID__']);
        $this->createUrl = InterventionResource::getUrl('create');

        $this->statutOptions = collect(StatutIntervention::cases())
            ->map(fn (StatutIntervention $statut) => [
                'value' => $statut->value,
                'label' => $statut->getLabel(),
                'color' => InterventionColorResolver::hex($statut->getColor()),
            ])
            ->all();

        $this->typeOptions = collect(TypeIntervention::cases())
            ->map(fn (TypeIntervention $type) => [
                'value' => $type->value,
                'label' => $type->getLabel(),
                'color' => InterventionColorResolver::hex($type->getColor()),
            ])
            ->all();

        $this->technicienOptions = User::role('Technicien')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (User $u) => ['value' => $u->id, 'label' => $u->name])
            ->all();

        $this->equipementOptions = Equipement::orderBy('nom')
            ->get(['id', 'nom'])
            ->map(fn (Equipement $e) => ['value' => $e->id, 'label' => $e->nom])
            ->all();
    }
}
