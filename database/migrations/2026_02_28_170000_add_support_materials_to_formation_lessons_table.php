<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('formation_lessons', function (Blueprint $table) {
            $table->text('support_text')->nullable()->after('description');
            $table->string('support_document_path')->nullable()->after('video_path');
        });
    }

    public function down(): void
    {
        Schema::table('formation_lessons', function (Blueprint $table) {
            $table->dropColumn([
                'support_text',
                'support_document_path',
            ]);
        });
    }
};
