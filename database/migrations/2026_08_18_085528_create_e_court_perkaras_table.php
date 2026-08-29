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
        Schema::create('e_court_perkaras', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_register')->unique();
            $table->string('jenis_perdata'); // Gugatan, Permohonan dll
            $table->string('penggugat');
            $table->string('tergugat');
            $table->decimal('nominal_panjar', 15, 2);
            $table->string('status_pembayaran')->default('Belum Dibayar'); // Belum Dibayar, Lunas
            $table->dateTime('jadwal_sidang_online')->nullable();
            $table->string('link_litigasi_online')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_court_perkaras');
    }
};
