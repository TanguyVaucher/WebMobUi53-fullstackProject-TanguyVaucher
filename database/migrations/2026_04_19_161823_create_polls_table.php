<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Table polls : Table de stockage des sondages
     */
    public function up(): void
    {
        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); /* Un poll est lié à un utilisateur */
            $table->string('title')->nullable(); /* NON UTILISE - Un sondage est identifié par sa question */
            $table->string('question'); /* Question du sondage */
            $table->string('secret_token')->unique(); /* Token unique d'un sondage (utilisé dans l'url de partage) */
            $table->boolean('is_draft')->default(true); /* Gestion des brouillons */
            $table->boolean('allow_multiple_choices')->default(false); /* Gestion choix multiples (Autorisé ou non) */
            $table->boolean('allow_vote_change')->default(false); /* Gestion changement de vote (Autorisé ou non) */
            $table->boolean('results_public')->default(false); /* Gestion des résultats (publics ou privés) */
            $table->unsignedInteger('duration')->nullable()->comment('in seconds'); /* Durée du sondage - unsignedInteger = entier pos. ou null - en sec. */
            $table->timestamp('started_at')->nullable(); /* Début du sondage */
            $table->timestamp('ends_at')->nullable(); /* Fin du sondage */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('polls');
    }
};
