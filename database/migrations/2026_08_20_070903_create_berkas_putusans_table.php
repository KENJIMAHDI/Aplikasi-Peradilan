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
        Schema::create('berkas_putusans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_perkara');
            $table->string('file_asli')->nullable();
            $table->string('file_anonim')->nullable();
            $table->boolean('is_anonim_selesai')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berkas_putusans');
    }
};
