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
        Schema::table('interventions', function (Blueprint $table) {
            // Utilisateur (Agent) à l'origine du signalement — conserve l'historique si le compte part.
            $table->foreignId('demandeur_id')->nullable()->after('technicien_id')
                ->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interventions', function (Blueprint $table) {
            $table->dropForeign(['demandeur_id']);
            $table->dropColumn('demandeur_id');
        });
    }
};
