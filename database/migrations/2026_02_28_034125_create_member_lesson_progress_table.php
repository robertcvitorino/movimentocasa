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
        Schema::create('member_lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_formation_progress_id')->constrained('member_formation_progress')->cascadeOnDelete();
            $table->foreignId('formation_lesson_id')->constrained()->cascadeOnDelete();
            $table->string('status', 30)->index();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('last_watched_at')->nullable();
            $table->unsignedInteger('watch_seconds')->default(0);
            $table->timestamps();

            $table->unique(['member_formation_progress_id', 'formation_lesson_id'], 'mlp_progress_lesson_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_lesson_progress');
    }
};
