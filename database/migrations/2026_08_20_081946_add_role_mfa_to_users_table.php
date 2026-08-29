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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('Masyarakat')->after('email'); // Admin, Hakim, Masyarakat
            }
            $table->string('mfa_secret')->nullable()->after('role'); // OTP/TOTP secret
            $table->boolean('mfa_enabled')->default(false)->after('mfa_secret');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = ['mfa_secret', 'mfa_enabled'];
            if (Schema::hasColumn('users', 'role')) {
                $cols[] = 'role';
            }
            $table->dropColumn($cols);
        });
    }
};
