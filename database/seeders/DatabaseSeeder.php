<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\BarangKeluarItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'gudangpusat@wms.com'],
            [
                'name' => 'Gudang Pusat',
                'password' => Hash::make('password123'),
            ]
        );

        // Contoh data sesuai spesifikasi:
        // 1. Warning (menuju ~1 tahun lagi): 5/11/26 -> 12/12/27 (1 tahun, 1 bulan, 7 hari)
        Barang::updateOrCreate(
            ['no' => '001'],
            [
                'supplier' => 'PT. Derma Medika Utama',
                'nama_barang' => 'ACNE P - 0',
                'jumlah' => 100,
                'tanggal_masuk' => '2026-11-05',
                'tanggal_expired' => '2027-12-12',
                'kategori' => 'Skincare & Kosmetik',
                'satuan' => 'Pcs',
                'harga_barang' => 45000,
            ]
        );

        // 2. Safe (lebih dari 2-3 tahun)
        Barang::updateOrCreate(
            ['no' => '002'],
            [
                'supplier' => 'PT. Kosmetika Nusantara',
                'nama_barang' => 'BRIGHTENING SERUM VIT C',
                'jumlah' => 250,
                'tanggal_masuk' => '2026-11-05',
                'tanggal_expired' => '2029-11-10',
                'kategori' => 'Skincare & Kosmetik',
                'satuan' => 'Botol',
                'harga_barang' => 85000,
            ]
        );

        // 3. Danger (beberapa bulan lagi mendekati expired)
        Barang::updateOrCreate(
            ['no' => '003'],
            [
                'supplier' => 'CV. Farmasi Sejahtera',
                'nama_barang' => 'SOOTHING GEL ALOE 99%',
                'jumlah' => 40,
                'tanggal_masuk' => '2026-11-05',
                'tanggal_expired' => '2027-03-15',
                'kategori' => 'Skincare & Kosmetik',
                'satuan' => 'Tube',
                'harga_barang' => 38000,
            ]
        );

        // 4. Expired (sudah melebihi tanggal expired)
        Barang::updateOrCreate(
            ['no' => '004'],
            [
                'supplier' => 'PT. Derma Medika Utama',
                'nama_barang' => 'TONER HYDRATING BATCH LAMA',
                'jumlah' => 0,
                'tanggal_masuk' => '2025-01-10',
                'tanggal_expired' => '2026-10-01',
                'kategori' => 'Toner',
                'satuan' => 'Botol',
                'harga_barang' => 52000,
            ]
        );

        // Contoh Transaksi Barang Keluar (Sesuai Contoh PDF PT HSK BIODERMA)
        $suratJalan = BarangKeluar::updateOrCreate(
            ['no_surat' => '2710 /HSKBIO/ X /2026'],
            [
                'tanggal_keluar' => '2026-10-27',
                'pic' => 'RAHMA',
                'tujuan' => 'APOTEK',
                'nama_penerima' => 'AYU',
                'total_jumlah' => 373,
            ]
        );

        if ($suratJalan->items()->count() === 0) {
            $sampleItems = [
                ['no_barang' => '001', 'nama_barang' => 'MILKY MICELLAR', 'jumlah' => 5, 'tanggal_expired' => '2028-03-01'],
                ['no_barang' => '002', 'nama_barang' => 'WL SUNSCREEN', 'jumlah' => 5, 'tanggal_expired' => '2028-10-01'],
                ['no_barang' => '003', 'nama_barang' => 'BRIGHTENING COMPLEX', 'jumlah' => 3, 'tanggal_expired' => '2028-04-01'],
                ['no_barang' => '004', 'nama_barang' => 'P-0 NIGHT CREAM', 'jumlah' => 5, 'tanggal_expired' => '2028-08-01'],
                ['no_barang' => '005', 'nama_barang' => 'ACNE NIGHT CREAM', 'jumlah' => 2, 'tanggal_expired' => '2027-11-01'],
                ['no_barang' => '006', 'nama_barang' => 'LG-3 NIGHT CREAM', 'jumlah' => 3, 'tanggal_expired' => '2028-03-01'],
                ['no_barang' => '007', 'nama_barang' => 'MASKER VITAMIN C', 'jumlah' => 20, 'tanggal_expired' => '2028-08-01'],
                ['no_barang' => '008', 'nama_barang' => 'ORLIBES', 'jumlah' => 90, 'tanggal_expired' => '2028-07-01'],
                ['no_barang' => '009', 'nama_barang' => 'CB (STARCH BLOCKER)', 'jumlah' => 180, 'tanggal_expired' => '2029-09-01'],
                ['no_barang' => '010', 'nama_barang' => 'FC9', 'jumlah' => 60, 'tanggal_expired' => null],
            ];

            foreach ($sampleItems as $row) {
                BarangKeluarItem::create([
                    'barang_keluar_id' => $suratJalan->id,
                    'no_barang' => $row['no_barang'],
                    'nama_barang' => $row['nama_barang'],
                    'jumlah' => $row['jumlah'],
                    'tanggal_expired' => $row['tanggal_expired'],
                ]);
            }
        }

        // Contoh Sales Invoice sesuai contoh PDF (KEISHA SKINCARE - INV/26/HBA-V/003)
        $sampleInvoice = Invoice::updateOrCreate(
            ['no_invoice' => 'INV/26/HBA-V/003'],
            [
                'tanggal_invoice' => '2026-10-01',
                'tujuan' => 'KEISHA SKINCARE',
                'nama_tempat' => 'Jl. Ir. H. Juanda No.85, Bojongherang, Kec. Cianjur, Kabupaten Cianjur, Jawa Barat 43216',
                'dari_tanggal' => '2026-10-01',
                'ke_tanggal' => '2026-10-31',
                'sub_total' => 15815000,
                'diskon_persen' => 20,
                'diskon_nominal' => 3163000,
                'total_tagihan' => 12652000,
            ]
        );

        if ($sampleInvoice->items()->count() === 0) {
            $invoiceRows = [
                ['item_description' => 'Micellar Milk', 'qty' => 7, 'item_unit' => 'pcs', 'unit_price' => 100000, 'amount' => 700000],
                ['item_description' => 'Milky Micellar', 'qty' => 8, 'item_unit' => 'pcs', 'unit_price' => 100000, 'amount' => 800000],
                ['item_description' => 'Flawless Skin', 'qty' => 2, 'item_unit' => 'pcs', 'unit_price' => 120000, 'amount' => 240000],
                ['item_description' => 'WL Sunscreen', 'qty' => 2, 'item_unit' => 'pcs', 'unit_price' => 220000, 'amount' => 440000],
                ['item_description' => 'Essential Moist', 'qty' => 38, 'item_unit' => 'pcs', 'unit_price' => 200000, 'amount' => 7600000],
                ['item_description' => 'AA Skin Booster', 'qty' => 1, 'item_unit' => 'pcs', 'unit_price' => 300000, 'amount' => 300000],
                ['item_description' => 'AC-1', 'qty' => 2, 'item_unit' => 'pcs', 'unit_price' => 150000, 'amount' => 300000],
                ['item_description' => 'AC-3', 'qty' => 6, 'item_unit' => 'pcs', 'unit_price' => 200000, 'amount' => 1200000],
                ['item_description' => 'Acne Reducer', 'qty' => 1, 'item_unit' => 'pcs', 'unit_price' => 125000, 'amount' => 125000],
                ['item_description' => 'GL 02 Padat', 'qty' => 1, 'item_unit' => 'pcs', 'unit_price' => 150000, 'amount' => 150000],
                ['item_description' => 'Hand Body Lotion', 'qty' => 6, 'item_unit' => 'pcs', 'unit_price' => 180000, 'amount' => 1080000],
                ['item_description' => 'FC9 @30', 'qty' => 30, 'item_unit' => 'pcs', 'unit_price' => 20000, 'amount' => 600000],
                ['item_description' => "S-thione '30", 'qty' => 120, 'item_unit' => 'pcs', 'unit_price' => 19000, 'amount' => 2280000],
            ];

            foreach ($invoiceRows as $row) {
                InvoiceItem::create([
                    'invoice_id' => $sampleInvoice->id,
                    'item_description' => $row['item_description'],
                    'qty' => $row['qty'],
                    'item_unit' => $row['item_unit'],
                    'unit_price' => $row['unit_price'],
                    'amount' => $row['amount'],
                ]);
            }
        }
    }
}
