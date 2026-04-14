<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('formations', function (Blueprint $table) {
            $table->string('approval_status', 30)->nullable()->after('status');
            $table->text('approval_notes')->nullable()->after('approval_status');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete()->after('approval_notes');
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            $table->timestamp('submitted_for_review_at')->nullable()->after('reviewed_at');
        });
    }

    public function down(): void
    {
        Schema::table('formations', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn([
                'approval_status',
                'approval_notes',
                'reviewed_by',
                'reviewed_at',
                'submitted_for_review_at',
            ]);
        });
    }
};
