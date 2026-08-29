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
        Schema::table('e_court_perkaras', function (Blueprint $table) {
            $table->string('nik_penggugat')->nullable();
            $table->string('no_wa_penggugat')->nullable();
            $table->text('alamat_penggugat')->nullable();
            $table->string('file_ktp')->nullable();
            $table->string('file_gugatan')->nullable();
            $table->string('status_bayar')->default('belum_dibayar'); // belum_dibayar, lunas
            $table->string('status_verifikasi')->default('draft'); // draft, terverifikasi
            $table->string('nomor_va')->nullable();
            $table->text('catatan_khusus')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('e_court_perkaras', function (Blueprint $table) {
            $table->dropColumn([
                'nik_penggugat',
                'no_wa_penggugat',
                'alamat_penggugat',
                'file_ktp',
                'file_gugatan',
                'status_bayar',
                'status_verifikasi',
                'nomor_va',
                'catatan_khusus'
            ]);
        });
    }
};
