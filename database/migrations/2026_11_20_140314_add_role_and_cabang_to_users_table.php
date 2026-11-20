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
            // role: superadmin, gudang_pusat, ruang_dokter, gudang_cabang, apotek
            $table->string('role', 50)->default('gudang_pusat')->after('email');
            // cabang: Gudang Pusat, Ruang Dokter Cianjur, Gudang Bandung, dll.
            $table->string('cabang', 100)->nullable()->after('role');
            $table->string('pic', 100)->nullable()->after('cabang');
            $table->boolean('is_active')->default(true)->after('pic');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'cabang', 'pic', 'is_active']);
        });
    }
};
