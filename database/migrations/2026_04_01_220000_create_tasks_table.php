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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->string('priority', 20)->index();
            $table->foreignId('ministry_id')->nullable()->constrained()->nullOnDelete();
            $table->string('responsible_type', 20)->index();
            $table->foreignId('responsible_member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->foreignId('responsible_ministry_id')->nullable()->constrained('ministries')->nullOnDelete();
            $table->string('attachment_path')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['start_datetime', 'end_datetime']);
            $table->index(['responsible_type', 'responsible_member_id']);
            $table->index(['responsible_type', 'responsible_ministry_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
