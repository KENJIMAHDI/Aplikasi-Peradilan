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
            $table->date('tanggal_daftar')->nullable()->after('nomor_register');
            $table->string('status')->default('Diajukan')->after('tergugat'); // Diajukan, Sedang Di Proses, Selesai
            $table->decimal('nominal_panjar', 15, 2)->nullable()->change();
            $table->string('status_pembayaran')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('e_court_perkaras', function (Blueprint $table) {
            $table->dropColumn(['tanggal_daftar', 'status']);
            $table->decimal('nominal_panjar', 15, 2)->nullable(false)->change();
            $table->string('status_pembayaran')->nullable(false)->change();
        });
    }
};
