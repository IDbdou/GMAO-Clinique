<?php

namespace App\Support\Calendar;

use Filament\Facades\Filament;
use Filament\Support\Colors\Color;

/**
 * Resout la couleur d'un cas d'enum Filament (HasColor) vers une couleur CSS
 * unique, en reutilisant exactement la palette configuree sur le panel admin
 * (App\Providers\Filament\AdminPanelProvider) plutot qu'une map dupliquee.
 */
class InterventionColorResolver
{
    public static function hex(string|array $color): string
    {
        if (is_array($color)) {
            return $color[500] ?? (string) reset($color);
        }

        $panelColors = Filament::getPanel('admin')->getColors();

        if (isset($panelColors[$color])) {
            $panelColor = $panelColors[$color];

            return is_array($panelColor) ? ($panelColor[500] ?? (string) reset($panelColor)) : $panelColor;
        }

        // Couleurs semantiques non redefinies par le panel (ex: "gray") :
        // on retombe sur la palette par defaut de Filament.
        $default = match ($color) {
            'gray' => Color::Gray,
            default => Color::Gray,
        };

        return $default[500];
    }
}
