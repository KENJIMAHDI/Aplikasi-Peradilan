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
        Schema::create('e_raterangs', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_permohonan')->unique();
            $table->string('nik_pemohon', 16);
            $table->string('nama_pemohon');
            $table->string('jenis_surat');
            $table->string('status_verifikasi')->default('Belum Diverifikasi'); // Belum Diverifikasi, Selesai
            $table->string('file_surat_keterangan_pdf')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_raterangs');
    }
};
