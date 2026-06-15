<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Run the migrations.
 * Ajout d'un attribut dans la table polls pour définir la couleur de thème d'un sondage
 */

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('polls', function (Blueprint $table) {
            $table->string('color')->nullable()->after('secret_token'); /* Nom de la couleur */
        });
    }

    public function down(): void
    {
        Schema::table('polls', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};
