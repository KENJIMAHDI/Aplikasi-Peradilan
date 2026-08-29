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
        Schema::create('perkara_pidanas', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_perkara');
            $table->string('terdakwa');
            $table->string('jaksa');
            $table->string('pasal')->nullable();
            $table->string('status')->default('Proses');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perkara_pidanas');
    }
};
