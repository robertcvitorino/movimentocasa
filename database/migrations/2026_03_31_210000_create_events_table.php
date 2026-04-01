<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->string('location')->nullable();
            $table->string('color', 7)->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_type', 20)->nullable()->index();
            $table->date('recurrence_until')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['start_datetime', 'end_datetime']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
