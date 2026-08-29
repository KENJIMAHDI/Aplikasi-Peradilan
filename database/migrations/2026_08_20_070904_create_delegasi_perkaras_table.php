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
        Schema::create('delegasi_perkaras', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_perkara');
            $table->string('pengadilan_tujuan');
            $table->enum('status', ['Proses', 'Selesai'])->default('Proses');
            $table->string('file_surat_delegasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delegasi_perkaras');
    }
};
