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
        Schema::create('compte_rendus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intervention_id')->constrained('interventions')->cascadeOnDelete();
            $table->foreignId('technicien_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('observations')->nullable();
            $table->text('pieces_utilisees')->nullable();
            $table->decimal('temps_passe', 5, 2)->nullable();
            $table->decimal('cout_main_oeuvre', 10, 2)->nullable();
            $table->decimal('cout_pieces', 10, 2)->nullable();
            $table->decimal('cout_total', 10, 2)->nullable();
            $table->string('statut')->default('brouillon');
            $table->timestamp('date_soumission')->nullable();
            $table->timestamp('date_validation')->nullable();
            $table->text('commentaire_validation')->nullable();
            $table->string('signature_technicien')->nullable();
            $table->string('signature_chef_service')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compte_rendus');
    }
};
