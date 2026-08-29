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
        Schema::create('jadwal_sidangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hakim_id')->constrained('hakims')->onDelete('cascade');
            $table->foreignId('ruang_sidang_id')->constrained('ruang_sidangs')->onDelete('cascade');
            $table->string('nomor_perkara');
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai');
            $table->string('status')->default('TERJADWAL');
            $table->string('status_relaas')->default('Belum Dipanggil'); // Belum Dipanggil, Relaas Siap/Patut
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_sidangs');
    }
};
