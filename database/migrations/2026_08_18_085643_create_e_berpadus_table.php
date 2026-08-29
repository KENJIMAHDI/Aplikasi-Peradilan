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
        Schema::create('e_berpadus', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->unique();
            $table->string('instansi_pengaju'); // Kepolisian/Kejaksaan
            $table->string('jenis_permohonan'); // Penahanan/Penggeledahan dll
            $table->string('nama_tersangka');
            $table->string('status_persetujuan_hakim')->default('Menunggu'); // Menunggu, Disetujui, Ditolak
            $table->string('berkas_pdf')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_berpadus');
    }
};
