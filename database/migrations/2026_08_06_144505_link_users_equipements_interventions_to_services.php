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
        // Users : remplacer le texte libre "service" par une clé étrangère.
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('service');
            $table->foreignId('service_id')->nullable()->after('email')->constrained('services')->nullOnDelete();
        });

        // Equipements : remplacer le texte libre "service" par une clé étrangère.
        Schema::table('equipements', function (Blueprint $table) {
            $table->dropColumn('service');
            $table->foreignId('service_id')->nullable()->after('localisation')->constrained('services')->nullOnDelete();
        });

        // Interventions : ajouter le rattachement au service (déduit de l'équipement ou du demandeur).
        Schema::table('interventions', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->after('equipement_id')->constrained('services')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');
            $table->string('service')->nullable()->after('email');
        });

        Schema::table('equipements', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');
            $table->string('service')->nullable()->after('localisation');
        });

        Schema::table('interventions', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');
        });
    }
};
