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
        Schema::create('interventions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipement_id')->constrained('equipements')->cascadeOnDelete();
            // Technicien assigné : nullable, et on garde l'historique si le compte est supprimé.
            $table->foreignId('technicien_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('titre');
            $table->string('type')->default('curatif');       // curatif | preventif
            $table->string('priorite')->default('normale');
            $table->string('statut')->default('ouverte');
            $table->text('description')->nullable();
            $table->text('rapport')->nullable();               // compte-rendu d'intervention
            $table->timestamp('date_demande')->nullable();
            $table->timestamp('date_planifiee')->nullable();
            $table->timestamp('date_debut')->nullable();
            $table->timestamp('date_fin')->nullable();
            $table->decimal('cout', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interventions');
    }
};
