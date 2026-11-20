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
        Schema::table('barangs', function (Blueprint $table) {
            $table->string('cabang', 100)->default('Gudang Pusat')->index()->after('alokasi_penyimpanan');
            $table->foreignId('user_id')->nullable()->after('cabang')->constrained('users')->nullOnDelete();
        });

        Schema::table('barang_keluars', function (Blueprint $table) {
            $table->string('cabang_asal', 100)->default('Gudang Pusat')->index()->after('tujuan');
            $table->foreignId('user_id')->nullable()->after('cabang_asal')->constrained('users')->nullOnDelete();
        });

        Schema::table('permintaan_barangs', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('pic')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permintaan_barangs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id']);
        });

        Schema::table('barang_keluars', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['cabang_asal', 'user_id']);
        });

        Schema::table('barangs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['cabang', 'user_id']);
        });
    }
};
