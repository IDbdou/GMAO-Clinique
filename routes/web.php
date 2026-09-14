<?php

use App\Http\Controllers\Admin\InterventionCalendarController;
use App\Http\Controllers\CompteRenduPdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('gmao-landing');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/compte-rendu/{compteRendu}/pdf', [CompteRenduPdfController::class, 'download'])
        ->name('compte-rendu.pdf');
});

// Pas de middleware "auth" classique ici : ces routes sont consommees en
// JSON par le calendrier (fetch) et doivent repondre 401/403 en JSON plutot
// que rediriger vers une page de connexion. L'autorisation est verifiee
// explicitement dans le controleur (voir authorizeAdminPanel()).
Route::prefix('admin/calendrier')->name('admin.calendrier.')->group(function () {
    Route::get('/events', [InterventionCalendarController::class, 'events'])->name('events');
    Route::get('/unscheduled', [InterventionCalendarController::class, 'unscheduled'])->name('unscheduled');
    Route::patch('/interventions/{intervention}', [InterventionCalendarController::class, 'reschedule'])->name('reschedule');
});
