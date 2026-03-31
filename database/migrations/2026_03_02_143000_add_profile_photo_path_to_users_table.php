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

        DB::table('users')
            ->join('members', 'members.user_id', '=', 'users.id')
            ->whereNull('users.profile_photo_path')
            ->whereNotNull('members.profile_photo_path')
            ->update([
                'users.profile_photo_path' => DB::raw('members.profile_photo_path'),
            ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('profile_photo_path');
        });
    }
};
