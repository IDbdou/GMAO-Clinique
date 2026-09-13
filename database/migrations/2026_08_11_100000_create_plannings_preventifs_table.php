<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plannings_preventifs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipement_id')->constrained()->onDelete('cascade');
            $table->string('titre')->index();
            $table->text('description')->nullable();
            $table->string('frequence', 20); // mensuelle, trimestrielle, semestrielle, annuelle
            $table->integer('delai_alerte_jours')->default(7); // alerte X jours avant échéance
            $table->date('derniere_date')->nullable(); // dernière maintenance effectuée
            $table->date('prochaine_date'); // prochaine échéance
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plannings_preventifs');
    }
};
