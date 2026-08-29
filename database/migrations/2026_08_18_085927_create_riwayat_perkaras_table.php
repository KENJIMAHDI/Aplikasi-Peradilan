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
        Schema::create('riwayat_perkaras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_sidang_id')->constrained('jadwal_sidangs')->onDelete('cascade');
            $table->date('tanggal_sidang');
            $table->string('agenda');
            $table->text('hasil_sidang')->nullable();
            $table->text('amar_putusan')->nullable();
            $table->string('status_perkara')->default('Proses'); // Proses, Putus, Banding
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_perkaras');
    }
};
