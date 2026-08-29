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
            $table->enum('status_penggugat', ['belum_hadir', 'hadir', 'izin'])->default('belum_hadir');
            $table->enum('status_tergugat', ['belum_hadir', 'hadir', 'izin'])->default('belum_hadir');
            $table->enum('status_kelengkapan', ['belum_lengkap', 'siap_sidang'])->default('belum_lengkap');
            $table->string('no_hp_penggugat')->nullable();
            $table->string('no_hp_tergugat')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'no_hp')) {
                $table->string('no_hp')->nullable();
            }
        });

        Schema::table('hakims', function (Blueprint $table) {
            if (!Schema::hasColumn('hakims', 'no_hp')) {
                $table->string('no_hp')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_sidangs', function (Blueprint $table) {
            $table->dropColumn([
                'status_penggugat',
                'status_tergugat',
                'status_kelengkapan',
                'no_hp_penggugat',
                'no_hp_tergugat'
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'no_hp')) {
                $table->dropColumn('no_hp');
            }
        });

        Schema::table('hakims', function (Blueprint $table) {
            if (Schema::hasColumn('hakims', 'no_hp')) {
                $table->dropColumn('no_hp');
            }
        });
    }
};
