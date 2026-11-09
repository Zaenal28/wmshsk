<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\BarangKeluarItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Racikan;
use App\Models\RacikanBahan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

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

        // Bersihkan seluruh data proses sebelumnya agar mulai dari awal untuk pengujian
        Schema::disableForeignKeyConstraints();
        InvoiceItem::truncate();
        Invoice::truncate();
        BarangKeluarItem::truncate();
        BarangKeluar::truncate();
        RacikanBahan::truncate();
        Racikan::truncate();
        Barang::truncate();
        Schema::enableForeignKeyConstraints();

        // Import 165 data barang dari file CSV/Excel yang diberikan
        $csvPath = database_path('seeders/master_barang.csv');
        if (! file_exists($csvPath)) {
            return;
        }

        $handle = fopen($csvPath, 'r');
        if (! $handle) {
            return;
        }

        // Skip baris header
        fgetcsv($handle);

        $now = now();
        $rows = [];

        while (($data = fgetcsv($handle, 2000, ',')) !== false) {
            if (count($data) < 10) {
                continue;
            }

            $no = trim($data[0]);
            $namaBarang = trim($data[1]);
            $tanggalMasuk = trim($data[2]);
            $kategori = trim($data[3]);
            $alokasi = trim($data[4]);
            $supplier = trim($data[5]);
            $jumlah = (int) trim($data[6]);
            $tanggalExpired = trim($data[7]);
            $satuan = trim($data[8]) ?: 'pcs';
            $harga = (float) preg_replace('/[^0-9.]/', '', $data[9]);

            // Normalisasi typo "Makser" -> "Masker"
            if (strtolower($kategori) === 'makser') {
                $kategori = 'Masker';
            }

            $statusRacikan = strtolower($kategori) === 'bahan racik'
                ? 'Bahan Racik'
                : 'Non Racikan';

            $rows[] = [
                'no' => $no,
                'nama_barang' => $namaBarang,
                'status_racikan' => $statusRacikan,
                'tanggal_masuk' => $now->format('Y-m-d'),
                'kategori' => $kategori,
                'alokasi_penyimpanan' => $alokasi,
                'supplier' => $supplier,
                'jumlah' => $jumlah,
                'tanggal_expired' => $tanggalExpired ?: $now->addYear()->format('Y-m-d'),
                'satuan' => $satuan,
                'harga_barang' => $harga,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        fclose($handle);

        foreach (array_chunk($rows, 100) as $chunk) {
            DB::table('barangs')->insert($chunk);
        }
    }
}
