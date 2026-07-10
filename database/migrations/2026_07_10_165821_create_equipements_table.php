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
        Schema::create('equipements', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('code_inventaire')->unique();
            $table->string('numero_serie')->nullable();
            $table->string('marque')->nullable();
            $table->string('modele')->nullable();
            $table->string('service')->nullable();       // Radiologie, Bloc opératoire, Hémodialyse…
            $table->string('localisation')->nullable();   // Salle / emplacement précis
            $table->string('criticite')->default('moyenne');
            $table->string('statut')->default('en_service');
            $table->date('date_mise_en_service')->nullable();
            $table->string('fournisseur')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipements');
    }
};
