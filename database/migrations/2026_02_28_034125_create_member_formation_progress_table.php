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
        Schema::create('member_formation_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('formation_id')->constrained()->cascadeOnDelete();
            $table->string('status', 30)->index();
            $table->decimal('progress_percentage', 5, 2)->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('last_accessed_at')->nullable();
            $table->unsignedInteger('required_lessons_count')->default(0);
            $table->unsignedInteger('completed_required_lessons_count')->default(0);
            $table->decimal('quiz_score', 5, 2)->nullable();
            $table->timestamp('quiz_passed_at')->nullable();
            $table->timestamp('certificate_issued_at')->nullable();
            $table->timestamps();

            $table->unique(['member_id', 'formation_id']);
            $table->index(['formation_id', 'status']);
            $table->index(['member_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_formation_progress');
    }
};
