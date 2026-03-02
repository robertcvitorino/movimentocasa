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
        Schema::create('member_contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('reference_month');
            $table->unsignedSmallInteger('reference_year');
            $table->string('contribution_type', 30)->index();
            $table->decimal('expected_amount', 12, 2)->nullable();
            $table->decimal('declared_amount', 12, 2)->nullable();
            $table->string('payment_method', 30)->index();
            $table->string('status', 30)->index();
            $table->string('receipt_path')->nullable();
            $table->timestamp('declared_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['member_id', 'reference_year', 'reference_month'], 'member_contrib_reference_index');
            $table->unique(['member_id', 'reference_year', 'reference_month', 'contribution_type'], 'member_contrib_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_contributions');
    }
};
