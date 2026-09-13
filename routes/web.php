<?php

use App\Http\Controllers\CompteRenduPdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('gmao-landing');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/compte-rendu/{compteRendu}/pdf', [CompteRenduPdfController::class, 'download'])
        ->name('compte-rendu.pdf');
});
