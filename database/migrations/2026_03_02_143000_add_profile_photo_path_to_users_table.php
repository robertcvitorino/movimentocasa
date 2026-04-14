<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('profile_photo_path')->nullable()->after('email');
        });

        if (! Schema::hasColumn('members', 'profile_photo_path')) {
            return;
        }

        DB::statement('
            UPDATE users
            SET profile_photo_path = (
                SELECT members.profile_photo_path
                FROM members
                WHERE members.user_id = users.id
                  AND members.profile_photo_path IS NOT NULL
            )
            WHERE profile_photo_path IS NULL
              AND EXISTS (
                SELECT 1 FROM members
                WHERE members.user_id = users.id
                  AND members.profile_photo_path IS NOT NULL
            )
        ');
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('profile_photo_path');
        });
    }
};
