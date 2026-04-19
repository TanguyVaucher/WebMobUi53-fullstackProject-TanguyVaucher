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
        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->string('question');
            $table->string('secret_token')->unique();
            $table->boolean('is_draft')->default(true);
            $table->boolean('allow_multiple_choices')->default(false);
            $table->boolean('allow_vote_change')->default(false);
            $table->boolean('results_public')->default(false);
            $table->unsignedInteger('duration')->nullable()->comment('in seconds');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ends_at')->nullable();
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
