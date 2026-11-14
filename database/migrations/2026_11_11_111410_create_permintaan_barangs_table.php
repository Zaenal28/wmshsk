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
        Schema::create('permintaan_barangs', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat')->unique(); // Format: HSK/001/11-11-2026
            $table->date('tanggal_permintaan');
            $table->string('cabang'); // Nama cabang pemohon
            $table->string('pic')->default('ADMIN GUDANG'); // PIC pembuat/pengelola
            $table->integer('jumlah_barang')->default(0); // Total quantity barang yang diminta
            $table->string('status')->default('Menunggu'); // Status: Menunggu, Disetujui, Diproses, Selesai, Ditolak
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('permintaan_barang_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permintaan_barang_id')->constrained('permintaan_barangs')->cascadeOnDelete();
            $table->foreignId('barang_id')->nullable()->constrained('barangs')->nullOnDelete();
            $table->string('nama_barang');
            $table->integer('jumlah')->default(1);
            $table->string('satuan')->nullable()->default('pcs');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan_barang_items');
        Schema::dropIfExists('permintaan_barangs');
    }
};
