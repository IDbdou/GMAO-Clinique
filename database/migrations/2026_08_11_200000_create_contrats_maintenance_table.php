<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrats_maintenance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipement_id')->constrained()->onDelete('cascade');
            $table->string('reference')->unique(); // N° de contrat
            $table->string('type_contrat', 50); // Contrat global, pièces, maintenance préventive
            $table->string('fournisseur');
            $table->string('contact', 255)->nullable();
            $table->date('date_debut');
            $table->date('date_fin');
            $table->decimal('cout_annuel', 12, 2)->default(0);
            $table->integer('delai_alerte_mois')->default(3); // alerte X mois avant fin
            $table->text('notes')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrats_maintenance');
    }
};
