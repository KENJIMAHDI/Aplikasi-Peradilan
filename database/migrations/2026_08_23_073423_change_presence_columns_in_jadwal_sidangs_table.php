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
        Schema::table('jadwal_sidangs', function (Blueprint $table) {
            $table->string('status_penggugat')->default('belum_hadir')->change();
            $table->string('status_tergugat')->default('belum_hadir')->change();
            $table->string('status_kelengkapan')->default('belum_lengkap')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_sidangs', function (Blueprint $table) {
            $table->enum('status_penggugat', ['belum_hadir', 'hadir', 'izin'])->default('belum_hadir')->change();
            $table->enum('status_tergugat', ['belum_hadir', 'hadir', 'izin'])->default('belum_hadir')->change();
            $table->enum('status_kelengkapan', ['belum_lengkap', 'siap_sidang'])->default('belum_lengkap')->change();
        });
    }
};
