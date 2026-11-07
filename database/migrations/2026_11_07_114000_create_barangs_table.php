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
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->string('no', 50)->index();
            $table->string('supplier', 150)->nullable()->index();
            $table->string('nama_barang', 200)->index();
            $table->integer('jumlah')->default(0)->index();
            $table->date('tanggal_masuk')->index();
            $table->date('tanggal_expired')->index();
            $table->string('kategori', 100)->nullable()->index();
            $table->string('satuan', 50)->default('Pcs');
            $table->decimal('harga_barang', 15, 2)->default(0);
            $table->timestamps();

            // Composite index untuk optimasi filter & sorting skala data besar
            $table->index(['tanggal_expired', 'jumlah']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
