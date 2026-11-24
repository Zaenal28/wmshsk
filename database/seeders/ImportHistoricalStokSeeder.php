<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\PerencanaanStok;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ImportHistoricalStokSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('data/raw_stok_sheets.txt');
        if (!file_exists($filePath)) {
            $this->command?->error("File tidak ditemukan: {$filePath}");
            return;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $currentMonth = null;
        $currentKategori = 'Lain-lain';
        $insertedCount = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Deteksi Judul Bulan
            if (str_contains($line, 'STOK GUDANG PUSAT NOVEMBER 2026')) {
                $currentMonth = '2026-11';
                $currentKategori = 'Pembersih';
                continue;
            } elseif (str_contains($line, 'STOK GUDANG PUSAT OKTOBER 2026')) {
                $currentMonth = '2026-10';
                $currentKategori = 'Pembersih';
                continue;
            } elseif (str_contains($line, 'STOK GUDANG PUSAT SEPTEMBER 2026')) {
                $currentMonth = '2026-09';
                $currentKategori = 'Pembersih';
                continue;
            } elseif (str_contains($line, 'STOK GUDANG PUSAT AGUSTUS 2026')) {
                $currentMonth = '2026-08';
                $currentKategori = 'Pembersih';
                continue;
            }

            if (!$currentMonth) continue;

            $cells = str_getcsv($line);
            $cells = array_map('trim', $cells);

            // Lewati header row
            if (isset($cells[0]) && in_array(strtolower($cells[0]), ['no.', 'no', ''])) {
                // Bisa jadi baris kategori jika cells[1] ada isi tapi cells[2] kosong
                if (!empty($cells[1]) && empty($cells[2]) && empty($cells[3])) {
                    $cleanedKat = trim($cells[1]);
                    if (!in_array(strtolower($cleanedKat), ['nama produk', '3 bulan', '2 bulan'])) {
                        $currentKategori = $cleanedKat;
                    }
                }
                continue;
            }

            // Cek jika baris adalah nama kategori yang diidentasikan
            $firstCol = $cells[0] ?? '';
            $secondCol = $cells[1] ?? '';

            if (empty($firstCol) || !is_numeric($firstCol)) {
                // Cek kemungkinan baris header kategori
                $candidateKat = !empty($firstCol) ? $firstCol : $secondCol;
                if (!empty($candidateKat) && empty($cells[2]) && empty($cells[3]) && empty($cells[4])) {
                    $currentKategori = trim($candidateKat);
                    continue;
                }
            }

            // Jika ada nomor urut
            $noUrut = is_numeric($firstCol) ? $firstCol : null;
            $namaBarang = $noUrut !== null ? ($cells[1] ?? '') : $firstCol;

            if (empty($namaBarang)) continue;

            $namaBarang = PerencanaanStok::standardizeProductName($namaBarang);

            // Bersihkan nama kategori jika tertempel
            if (str_contains(strtolower($namaBarang), 'pembersih') && empty($cells[2])) {
                $currentKategori = 'Pembersih';
                continue;
            }
            if (str_contains(strtolower($namaBarang), 'sabun') && empty($cells[2])) {
                $currentKategori = 'Sabun';
                continue;
            }
            if (str_contains(strtolower($namaBarang), 'sunblock') && empty($cells[2])) {
                $currentKategori = 'Sunblock';
                continue;
            }
            if (str_contains(strtolower($namaBarang), 'pelembab') && empty($cells[2])) {
                $currentKategori = 'Pelembab';
                continue;
            }
            if (str_contains(strtolower($namaBarang), 'serum') && empty($cells[2])) {
                $currentKategori = 'Serum';
                continue;
            }
            if (str_contains(strtolower($namaBarang), 'cream malam') && empty($cells[2])) {
                $currentKategori = 'Cream Malam';
                continue;
            }
            if (str_contains(strtolower($namaBarang), 'toner') && empty($cells[2])) {
                $currentKategori = 'Toner';
                continue;
            }
            if (str_contains(strtolower($namaBarang), 'masker') && empty($cells[2])) {
                $currentKategori = 'Masker';
                continue;
            }
            if (str_contains(strtolower($namaBarang), 'bedak') && empty($cells[2])) {
                $currentKategori = 'Bedak';
                continue;
            }
            if (str_contains(strtolower($namaBarang), 'obat') && empty($cells[2])) {
                $currentKategori = 'Obat';
                continue;
            }
            if (str_contains(strtolower($namaBarang), 'vit') && empty($cells[2])) {
                $currentKategori = 'Vitamin';
                continue;
            }
            if (str_contains(strtolower($namaBarang), 'treatment') && empty($cells[2])) {
                $currentKategori = 'Treatment Equipment';
                continue;
            }
            if (str_contains(strtolower($namaBarang), 'korean lux') && empty($cells[2])) {
                $currentKategori = 'Korean Lux';
                continue;
            }
            if (str_contains(strtolower($namaBarang), 'bahan racik') && empty($cells[2])) {
                $currentKategori = 'Bahan Racik';
                continue;
            }

            // Parsing data per bulan
            if ($currentMonth === '2026-08') {
                // AGUSTUS 2026 format:
                // no(0), Nama(1), R3(2), R2(3), R1(4), StokAwal(5), Masuk(6), AP(7), RF(8), MP(9), MP2(10), PWK(11), BDG(12), giveaway(13), retur(14), StokAkhir(15), Kekurangan3(16), Kekurangan2(17), Pesan(18)
                $rata3 = (int) ($cells[2] ?? 0);
                $rata2 = (int) ($cells[3] ?? 0);
                $rata1 = (int) ($cells[4] ?? 0);
                $stokAwal = (int) ($cells[5] ?? 0);
                $masukPabrik = (int) ($cells[6] ?? 0);
                $masukCabang = 0;
                $keluarAp = (int) ($cells[7] ?? 0);
                $keluarRf = (int) ($cells[8] ?? 0);
                $keluarMp = (int) ($cells[9] ?? 0) + (int) ($cells[10] ?? 0);
                $keluarPwk = (int) ($cells[11] ?? 0);
                $keluarBdg = (int) ($cells[12] ?? 0);
                $keluarRacik = (int) ($cells[13] ?? 0); // give away
                $returPabrik = (int) ($cells[14] ?? 0);
                $stokAkhir = !empty($cells[15]) && is_numeric($cells[15]) ? (int) $cells[15] : (($stokAwal + $masukPabrik) - ($keluarAp + $keluarRf + $keluarMp + $keluarPwk + $keluarBdg + $keluarRacik) - $returPabrik);
                $statusPesan = !empty($cells[18]) ? $cells[18] : (($rata3 > 0 && $stokAkhir < $rata3) ? 'Pesan' : 'Tidak');
                $tanggalPesan = null;
                $stokPabrik = null;
                $expired1 = null;
                $expired2 = null;
            } elseif ($currentMonth === '2026-09') {
                // SEPTEMBER 2026 format:
                // no(0), Nama(1), R3(2), R2(3), R1(4), StokAwal(5), MasukPabrik(6), MasukCabang(7), AP(8), RF(9), MP(10), PWK(11), BDG(12), RACIK(13), TotalKeluar(14), retur(15), StokAkhir(16), Kekurangan3(17), Kekurangan2(18), Pesan(19)
                $rata3 = (int) ($cells[2] ?? 0);
                $rata2 = (int) ($cells[3] ?? 0);
                $rata1 = (int) ($cells[4] ?? 0);
                $stokAwal = (int) ($cells[5] ?? 0);
                $masukPabrik = (int) ($cells[6] ?? 0);
                $masukCabang = (int) ($cells[7] ?? 0);
                $keluarAp = (int) ($cells[8] ?? 0);
                $keluarRf = (int) ($cells[9] ?? 0);
                $keluarMp = (int) ($cells[10] ?? 0);
                $keluarPwk = (int) ($cells[11] ?? 0);
                $keluarBdg = (int) ($cells[12] ?? 0);
                $keluarRacik = (int) ($cells[13] ?? 0);
                $returPabrik = (int) ($cells[15] ?? 0);
                $stokAkhir = !empty($cells[16]) && is_numeric($cells[16]) ? (int) $cells[16] : (($stokAwal + $masukPabrik + $masukCabang) - ($keluarAp + $keluarRf + $keluarMp + $keluarPwk + $keluarBdg + $keluarRacik) - $returPabrik);
                $statusPesan = !empty($cells[19]) ? $cells[19] : (($rata3 > 0 && $stokAkhir < $rata3) ? 'Pesan' : 'Tidak');
                $tanggalPesan = null;
                $stokPabrik = null;
                $expired1 = null;
                $expired2 = null;
            } elseif ($currentMonth === '2026-10') {
                // OKTOBER 2026 format:
                // no(0), Nama(1), R3(2), R2(3), R1(4), StokAwal(5), MasukPabrik(6), MasukCabang(7), AP(8), RF(9), MP(10), PWK(11), BDG(12), RACIK(13), TotalKeluar(14), retur(15), StokAkhir(16), Kekurangan3(17), Kekurangan2(18), Pesan(19), (20), TglPesan(21)
                $rata3 = (int) ($cells[2] ?? 0);
                $rata2 = (int) ($cells[3] ?? 0);
                $rata1 = (int) ($cells[4] ?? 0);
                $stokAwal = (int) ($cells[5] ?? 0);
                $masukPabrik = (int) ($cells[6] ?? 0);
                $masukCabang = (int) ($cells[7] ?? 0);
                $keluarAp = (int) ($cells[8] ?? 0);
                $keluarRf = (int) ($cells[9] ?? 0);
                $keluarMp = (int) ($cells[10] ?? 0);
                $keluarPwk = (int) ($cells[11] ?? 0);
                $keluarBdg = (int) ($cells[12] ?? 0);
                $keluarRacik = (int) ($cells[13] ?? 0);
                $returPabrik = (int) ($cells[15] ?? 0);
                $stokAkhir = !empty($cells[16]) && is_numeric($cells[16]) ? (int) $cells[16] : (($stokAwal + $masukPabrik + $masukCabang) - ($keluarAp + $keluarRf + $keluarMp + $keluarPwk + $keluarBdg + $keluarRacik) - $returPabrik);
                $statusPesan = !empty($cells[19]) ? $cells[19] : (($rata3 > 0 && $stokAkhir < $rata3) ? 'Pesan' : 'Tidak');
                $tanggalPesan = !empty($cells[21]) ? $this->parseTgl($cells[21]) : null;
                $stokPabrik = null;
                $expired1 = null;
                $expired2 = null;
            } else {
                // NOVEMBER 2026 format:
                // no(0), Nama(1), R3(2), R2(3), R1(4), StokAwal(5), MasukPabrik(6), MasukCabang(7), AP(8), RF(9), MP(10), PWK(11), BDG(12), RACIK(13), TotalKeluar(14), retur(15), StokAkhir(16), Kekurangan3(17), Kekurangan2(18), Pesan(19), (20), TglPesan(21), StokPabrik(22), (23), Expired(24)
                $rata3 = (int) ($cells[2] ?? 0);
                $rata2 = (int) ($cells[3] ?? 0);
                $rata1 = (int) ($cells[4] ?? 0);
                $stokAwal = (int) ($cells[5] ?? 0);
                $masukPabrik = (int) ($cells[6] ?? 0);
                $masukCabang = (int) ($cells[7] ?? 0);
                $keluarAp = (int) ($cells[8] ?? 0);
                $keluarRf = (int) ($cells[9] ?? 0);
                $keluarMp = (int) ($cells[10] ?? 0);
                $keluarPwk = (int) ($cells[11] ?? 0);
                $keluarBdg = (int) ($cells[12] ?? 0);
                $keluarRacik = (int) ($cells[13] ?? 0);
                $returPabrik = (int) ($cells[15] ?? 0);
                $stokAkhir = !empty($cells[16]) && is_numeric($cells[16]) ? (int) $cells[16] : (($stokAwal + $masukPabrik + $masukCabang) - ($keluarAp + $keluarRf + $keluarMp + $keluarPwk + $keluarBdg + $keluarRacik) - $returPabrik);
                $statusPesan = !empty($cells[19]) ? $cells[19] : (($rata3 > 0 && $stokAkhir < $rata3) ? 'Pesan' : 'Tidak');
                $tanggalPesan = !empty($cells[21]) ? $this->parseTgl($cells[21]) : null;
                $stokPabrik = !empty($cells[22]) && is_numeric($cells[22]) ? (int) $cells[22] : null;
                $expired1 = !empty($cells[24]) && $cells[24] !== '-' ? $cells[24] : null;
                $expired2 = null;
            }

            $totalKeluar = $keluarAp + $keluarRf + $keluarMp + $keluarPwk + $keluarBdg + $keluarRacik;

            // Cari relasi ke master Barang jika ada
            $barang = Barang::where('nama_barang', 'like', "%{$namaBarang}%")->first();

            // Normalisasi status pesan
            $cleanStatus = 'Tidak';
            if (str_contains(strtolower($statusPesan), 'discontinue')) {
                $cleanStatus = 'Discontinue';
            } elseif (str_contains(strtolower($statusPesan), 'pesan')) {
                $cleanStatus = 'Pesan';
            } elseif (str_contains(strtolower($statusPesan), 'racikan')) {
                $cleanStatus = 'Racikan';
            }

            PerencanaanStok::updateOrCreate(
                [
                    'periode' => $currentMonth,
                    'tipe_periode' => 'bulanan',
                    'nama_barang' => $namaBarang,
                ],
                [
                    'barang_id' => $barang?->id,
                    'no_urut' => $noUrut,
                    'kategori' => $currentKategori,
                    'rata_3_bulan' => $rata3,
                    'rata_2_bulan' => $rata2,
                    'rata_1_bulan' => $rata1,
                    'stok_awal' => $stokAwal,
                    'masuk_pabrik' => $masukPabrik,
                    'masuk_cabang' => $masukCabang,
                    'keluar_ap' => $keluarAp,
                    'keluar_rf' => $keluarRf,
                    'keluar_mp' => $keluarMp,
                    'keluar_pwk' => $keluarPwk,
                    'keluar_bdg' => $keluarBdg,
                    'keluar_racik' => $keluarRacik,
                    'total_keluar' => $totalKeluar,
                    'retur_pabrik' => $returPabrik,
                    'stok_akhir' => $stokAkhir,
                    'status_pesan' => $cleanStatus,
                    'tanggal_pesan' => $tanggalPesan,
                    'stok_pabrik' => $stokPabrik,
                    'expired_1' => $expired1,
                    'expired_2' => $expired2,
                ]
            );

            $insertedCount++;
        }

        $this->command?->info("Berhasil mengimpor {$insertedCount} baris data perencanaan stok untuk 4 bulan (Agustus, September, Oktober, November 2026)!");
    }

    private function parseTgl(?string $str): ?string
    {
        if (empty($str) || $str === '-') return null;
        try {
            return Carbon::parse($str)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
