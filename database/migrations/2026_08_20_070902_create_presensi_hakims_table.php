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
        Schema::create('presensi_hakims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hakim_id')->constrained('hakims')->onDelete('cascade');
            $table->date('tanggal');
            $table->enum('status', ['Hadir', 'Cuti', 'Dinas'])->default('Hadir');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi_hakims');
    }
};
