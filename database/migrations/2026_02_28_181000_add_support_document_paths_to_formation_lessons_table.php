<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('formation_lessons', function (Blueprint $table) {
            $table->json('support_document_paths')->nullable()->after('support_document_path');
        });

        DB::table('formation_lessons')
            ->whereNotNull('support_document_path')
            ->orderBy('id')
            ->lazy()
            ->each(function (object $lesson): void {
                DB::table('formation_lessons')
                    ->where('id', $lesson->id)
                    ->update([
                        'support_document_paths' => json_encode([$lesson->support_document_path], JSON_THROW_ON_ERROR),
                    ]);
            });
    }

    public function down(): void
    {
        Schema::table('formation_lessons', function (Blueprint $table) {
            $table->dropColumn('support_document_paths');
        });
    }
};
