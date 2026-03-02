<?php

use App\Enums\MemberMinistryStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member_ministry', function (Blueprint $table) {
            $table->string('status', 30)
                ->default(MemberMinistryStatus::Active->value)
                ->change();
        });
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE member_ministry ALTER COLUMN status DROP DEFAULT');
    }
};
