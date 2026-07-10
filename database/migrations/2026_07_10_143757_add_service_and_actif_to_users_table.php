<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Service d'affectation (Radiologie, Hémodialyse, Bloc opératoire, ...) — nullable pour l'Admin
            $table->string('service')->nullable()->after('email');
            // Permet de désactiver un accès sans supprimer le compte ni casser son historique
            $table->boolean('actif')->default(true)->after('service');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['service', 'actif']);
        });
    }
};
