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
        Schema::create('perencanaan_stoks', function (Blueprint $table) {
            $table->id();
            $table->string('periode', 20)->index(); // e.g. '2026-11', '2026-W47', '2026-11-24'
            $table->string('tipe_periode', 20)->default('bulanan')->index(); // bulanan, mingguan, harian
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->foreignId('barang_id')->nullable()->constrained('barangs')->nullOnDelete();
            $table->string('no_urut', 50)->nullable();
            $table->string('nama_barang');
            $table->string('kategori')->nullable()->index();
            $table->integer('rata_3_bulan')->default(0);
            $table->integer('rata_2_bulan')->default(0);
            $table->integer('rata_1_bulan')->default(0);
            $table->integer('stok_awal')->default(0);
            $table->integer('masuk_pabrik')->default(0);
            $table->integer('masuk_cabang')->default(0);
            $table->integer('keluar_ap')->default(0);
            $table->integer('keluar_rf')->default(0);
            $table->integer('keluar_mp')->default(0);
            $table->integer('keluar_pwk')->default(0);
            $table->integer('keluar_bdg')->default(0);
            $table->integer('keluar_racik')->default(0);
            $table->integer('total_keluar')->default(0);
            $table->integer('retur_pabrik')->default(0);
            $table->integer('stok_akhir')->default(0);
            $table->string('status_pesan', 30)->default('Tidak'); // Pesan, Tidak, Discontinue
            $table->date('tanggal_pesan')->nullable();
            $table->integer('stok_pabrik')->nullable();
            $table->string('expired_1', 50)->nullable();
            $table->string('expired_2', 50)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['periode', 'tipe_periode', 'nama_barang']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perencanaan_stoks');
    }
};
