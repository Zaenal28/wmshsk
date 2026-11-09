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
            // Pilihan: Non Racikan, Sudah Diracik, Belum Diracik, Bahan Racik
            $table->string('status_racikan', 50)->default('Non Racikan')->index()->after('nama_barang');
        });

        Schema::create('racikans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_racik', 50)->index();
            $table->date('tanggal_racik')->index();
            $table->foreignId('barang_asal_id')->nullable()->constrained('barangs')->nullOnDelete();
            $table->foreignId('barang_hasil_id')->nullable()->constrained('barangs')->nullOnDelete();
            $table->string('nama_barang', 200)->index();
            $table->integer('jumlah_racik')->default(0);
            $table->date('tanggal_expired_hasil')->nullable();
            $table->string('pic', 100)->nullable();
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('racikan_bahans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('racikan_id')->constrained('racikans')->cascadeOnDelete();
            $table->foreignId('bahan_barang_id')->nullable()->constrained('barangs')->nullOnDelete();
            $table->string('no_bahan', 50)->nullable();
            $table->string('nama_bahan', 200);
            $table->integer('jumlah_pakai')->default(0);
            $table->string('satuan', 50)->default('Pcs');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('racikan_bahans');
        Schema::dropIfExists('racikans');

        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn('status_racikan');
        });
    }
};
