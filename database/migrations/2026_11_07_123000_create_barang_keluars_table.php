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
        Schema::create('barang_keluars', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat', 100)->index();
            $table->date('tanggal_keluar')->index();
            $table->string('pic', 100)->index();
            $table->string('tujuan', 150)->index();
            $table->string('nama_penerima', 100)->nullable();
            $table->integer('total_jumlah')->default(0)->index();
            $table->timestamps();

            $table->index(['tanggal_keluar', 'tujuan']);
        });

        Schema::create('barang_keluar_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_keluar_id')->constrained('barang_keluars')->cascadeOnDelete();
            $table->foreignId('barang_id')->nullable()->constrained('barangs')->nullOnDelete();
            $table->string('no_barang', 50)->index();
            $table->string('nama_barang', 200);
            $table->date('tanggal_expired')->nullable();
            $table->integer('jumlah')->default(0);
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();

            $table->index(['barang_keluar_id', 'no_barang']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_keluar_items');
        Schema::dropIfExists('barang_keluars');
    }
};
