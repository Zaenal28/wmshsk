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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('no_invoice', 100)->index();
            $table->date('tanggal_invoice')->index();
            $table->string('tujuan', 150)->index();
            $table->string('nama_tempat', 200)->nullable();
            $table->text('alamat_tempat')->nullable();
            $table->date('dari_tanggal')->index();
            $table->date('ke_tanggal')->index();
            $table->decimal('sub_total', 15, 2)->default(0);
            $table->decimal('diskon_persen', 5, 2)->default(20);
            $table->decimal('diskon_nominal', 15, 2)->default(0);
            $table->decimal('total_tagihan', 15, 2)->default(0)->index();
            $table->timestamps();

            $table->index(['tujuan', 'dari_tanggal', 'ke_tanggal']);
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('barang_keluar_item_id')->nullable()->constrained('barang_keluar_items')->nullOnDelete();
            $table->foreignId('barang_id')->nullable()->constrained('barangs')->nullOnDelete();
            $table->string('no_surat_jalan', 100)->nullable();
            $table->date('tanggal_keluar')->nullable();
            $table->string('item_description', 200);
            $table->integer('qty')->default(0);
            $table->string('item_unit', 50)->default('pcs');
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            $table->timestamps();

            $table->index(['invoice_id', 'item_description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
