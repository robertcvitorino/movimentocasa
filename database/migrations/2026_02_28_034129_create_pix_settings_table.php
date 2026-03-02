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
        Schema::create('pix_settings', function (Blueprint $table) {
            $table->id();
            $table->string('pix_key');
            $table->string('beneficiary_name');
            $table->string('city')->nullable();
            $table->string('qr_code_image_path')->nullable();
            $table->text('copy_paste_code')->nullable();
            $table->text('instructions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pix_settings');
    }
};
