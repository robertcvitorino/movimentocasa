<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('formations', function (Blueprint $table): void {
            $table->boolean('is_general')->default(true)->after('is_required');
        });
    }

    public function down(): void
    {
        Schema::table('formations', function (Blueprint $table): void {
            $table->dropColumn('is_general');
        });
    }
};
