<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Table poll_votes : Stockage des votes pour les options des sondages
     */
    public function up(): void
    {
        Schema::create('poll_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained('polls')->onDelete('cascade'); /* Un vote est lié à un sondage */
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); /* Un vote est lié à un utilisateur */
            $table->foreignId('poll_option_id')->constrained('poll_options')->onDelete('cascade'); /* Un vote est lié à une option */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poll_votes');
    }
};
