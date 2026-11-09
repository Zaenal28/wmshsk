<?php

namespace App\Filament\Pages;

use App\Models\Barang;
use App\Models\Racikan;
use App\Models\RacikanBahan;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class KelolaRacikan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationLabel = 'Kelola Racikan';
    protected static ?string $title = 'Proses & Kelola Racikan Barang';
    protected static ?string $navigationGroup = 'Gudang & Inventori';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'kelola-racikan';

    protected static string $view = 'filament.pages.kelola-racikan';

    // Form Utama Peracikan
    public string $kode_racik = '';
    public string $tanggal_racik = '';
    public ?int $barang_asal_id = null;
    public int|string $jumlah_racik = '';
    public string $tanggal_expired_hasil = '';
    public string $pic = '';
    public string $keterangan = '';

    // Form Input Bahan Racik Campuran
    public ?int $selected_bahan_id = null;
    public int|string $input_jumlah_bahan = '';

    // Daftar Bahan Racik yang akan dicampurkan
    public array $bahanItems = [];

    public static function getNavigationBadge(): ?string
    {
        return (string) Racikan::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public function mount(): void
    {
        $this->resetFormUtama();
    }

    protected function resetFormUtama(): void
    {
        $urut = str_pad((string) (Racikan::count() + 1), 3, '0', STR_PAD_LEFT);
        $this->kode_racik = 'RCK-' . now()->format('Ym') . '-' . $urut;
        $this->tanggal_racik = now()->format('Y-m-d');
        $this->barang_asal_id = null;
        $this->jumlah_racik = '';
        $this->tanggal_expired_hasil = '';
        $this->pic = 'RAHMA';
        $this->keterangan = '';
        $this->selected_bahan_id = null;
        $this->input_jumlah_bahan = '';
        $this->bahanItems = [];
    }

    public function updatedBarangAsalId($value): void
    {
        if (! $value) {
            return;
        }
        $barang = Barang::find($value);
        if ($barang && $barang->tanggal_expired) {
            $this->tanggal_expired_hasil = $barang->tanggal_expired->format('Y-m-d');
        }
    }

    public function getBarangBelumDiracikProperty()
    {
        return Barang::query()
            ->where('status_racikan', 'Belum Diracik')
            ->where('jumlah', '>', 0)
            ->orderBy('nama_barang')
            ->get();
    }

    public function getDaftarBahanRacikProperty()
    {
        return Barang::query()
            ->where(function ($q) {
                $q->where('status_racikan', 'Bahan Racik')
                    ->orWhere('kategori', 'Bahan Racik');
            })
            ->where('jumlah', '>', 0)
            ->orderBy('nama_barang')
            ->get();
    }

    public function getRiwayatRacikansProperty()
    {
        return Racikan::query()
            ->with('bahans')
            ->orderBy('tanggal_racik', 'desc')
            ->orderBy('id', 'desc')
            ->get();
    }

    public function tambahBahanCampuran(): void
    {
        if (! $this->selected_bahan_id) {
            Notification::make()
                ->title('Pilih Bahan Racik terlebih dahulu.')
                ->warning()
                ->send();
            return;
        }

        $qty = (int) $this->input_jumlah_bahan;
        if ($qty <= 0) {
            Notification::make()
                ->title('Jumlah bahan racik harus lebih dari 0.')
                ->warning()
                ->send();
            return;
        }

        $bahan = Barang::find($this->selected_bahan_id);
        if (! $bahan) {
            return;
        }

        if ($qty > $bahan->jumlah) {
            Notification::make()
                ->title("Stok bahan '{$bahan->nama_barang}' tidak mencukupi (Tersedia: {$bahan->jumlah}).")
                ->danger()
                ->send();
            return;
        }

        $this->bahanItems[] = [
            'bahan_barang_id' => $bahan->id,
            'no_bahan' => $bahan->no,
            'nama_bahan' => $bahan->nama_barang,
            'jumlah_pakai' => $qty,
            'satuan' => $bahan->satuan ?: 'Pcs',
        ];

        $this->selected_bahan_id = null;
        $this->input_jumlah_bahan = '';
    }

    public function hapusBahanCampuran(int $index): void
    {
        if (isset($this->bahanItems[$index])) {
            unset($this->bahanItems[$index]);
            $this->bahanItems = array_values($this->bahanItems);
        }
    }

    public function prosesSimpanRacikan(): void
    {
        if (! $this->barang_asal_id) {
            Notification::make()
                ->title('Pilih Barang yang Belum Diracik terlebih dahulu.')
                ->warning()
                ->send();
            return;
        }

        $qtyRacik = (int) $this->jumlah_racik;
        if ($qtyRacik <= 0) {
            Notification::make()
                ->title('Jumlah barang yang akan diracik harus lebih dari 0.')
                ->warning()
                ->send();
            return;
        }

        $barangAsal = Barang::find($this->barang_asal_id);
        if (! $barangAsal || $qtyRacik > $barangAsal->jumlah) {
            Notification::make()
                ->title('Stok barang Belum Diracik tidak mencukupi untuk jumlah yang dimasukkan.')
                ->danger()
                ->send();
            return;
        }

        if (empty($this->bahanItems)) {
            Notification::make()
                ->title('Tambahkan minimal 1 Bahan Racik campuran ke daftar.')
                ->warning()
                ->send();
            return;
        }

        DB::transaction(function () use ($barangAsal, $qtyRacik) {
            // 1. Kurangi stok barang asal (Belum Diracik)
            $barangAsal->decrement('jumlah', $qtyRacik);

            // 2. Cari apakah di tabel barangs sudah ada barang dengan NAMA SAMA dan status "Sudah Diracik"
            $barangHasil = Barang::query()
                ->where('nama_barang', $barangAsal->nama_barang)
                ->where('status_racikan', 'Sudah Diracik')
                ->first();

            $expiredHasil = $this->tanggal_expired_hasil ?: $barangAsal->tanggal_expired?->format('Y-m-d');

            if ($barangHasil) {
                $barangHasil->increment('jumlah', $qtyRacik);
                if ($expiredHasil) {
                    $barangHasil->update(['tanggal_expired' => $expiredHasil]);
                }
            } else {
                $nextNo = str_pad((string) (Barang::count() + 1), 3, '0', STR_PAD_LEFT);
                $barangHasil = Barang::create([
                    'no' => $nextNo,
                    'supplier' => $barangAsal->supplier,
                    'nama_barang' => $barangAsal->nama_barang, // Nama tetap sama persis!
                    'status_racikan' => 'Sudah Diracik',
                    'jumlah' => $qtyRacik,
                    'tanggal_masuk' => $this->tanggal_racik ?: now()->format('Y-m-d'),
                    'tanggal_expired' => $expiredHasil ?: now()->addYear()->format('Y-m-d'),
                    'kategori' => $barangAsal->kategori,
                    'alokasi_penyimpanan' => $barangAsal->alokasi_penyimpanan,
                    'satuan' => $barangAsal->satuan,
                    'harga_barang' => $barangAsal->harga_barang,
                ]);
            }

            // 3. Simpan log transaksi peracikan
            $racikan = Racikan::create([
                'kode_racik' => $this->kode_racik,
                'tanggal_racik' => $this->tanggal_racik ?: now()->format('Y-m-d'),
                'barang_asal_id' => $barangAsal->id,
                'barang_hasil_id' => $barangHasil->id,
                'nama_barang' => $barangAsal->nama_barang,
                'jumlah_racik' => $qtyRacik,
                'tanggal_expired_hasil' => $expiredHasil,
                'pic' => $this->pic ?: 'Admin Gudang',
                'keterangan' => $this->keterangan,
            ]);

            // 4. Kurangi stok masing-masing Bahan Racik & simpan detailnya
            foreach ($this->bahanItems as $bItem) {
                RacikanBahan::create([
                    'racikan_id' => $racikan->id,
                    'bahan_barang_id' => $bItem['bahan_barang_id'],
                    'no_bahan' => $bItem['no_bahan'],
                    'nama_bahan' => $bItem['nama_bahan'],
                    'jumlah_pakai' => (int) $bItem['jumlah_pakai'],
                    'satuan' => $bItem['satuan'],
                ]);

                if (! empty($bItem['bahan_barang_id'])) {
                    Barang::where('id', $bItem['bahan_barang_id'])
                        ->where('jumlah', '>=', (int) $bItem['jumlah_pakai'])
                        ->decrement('jumlah', (int) $bItem['jumlah_pakai']);
                }
            }
        });

        Notification::make()
            ->title("Proses racik '{$barangAsal->nama_barang}' ({$qtyRacik} {$barangAsal->satuan}) selesai! Stok 'Sudah Diracik' otomatis bertambah.")
            ->success()
            ->send();

        $this->redirect(static::getUrl(), navigate: true);
    }

    public function hapusRiwayatRacikan(int $id): void
    {
        $racikan = Racikan::with('bahans')->find($id);

        if ($racikan) {
            DB::transaction(function () use ($racikan) {
                if ($racikan->barang_asal_id) {
                    Barang::where('id', $racikan->barang_asal_id)->increment('jumlah', $racikan->jumlah_racik);
                }
                if ($racikan->barang_hasil_id) {
                    Barang::where('id', $racikan->barang_hasil_id)
                        ->where('jumlah', '>=', $racikan->jumlah_racik)
                        ->decrement('jumlah', $racikan->jumlah_racik);
                }
                foreach ($racikan->bahans as $b) {
                    if ($b->bahan_barang_id) {
                        Barang::where('id', $b->bahan_barang_id)->increment('jumlah', $b->jumlah_pakai);
                    }
                }
                $racikan->delete();
            });

            Notification::make()
                ->title('Data racikan dibatalkan & stok dikembalikan.')
                ->success()
                ->send();
        }

        $this->redirect(static::getUrl(), navigate: true);
    }
}
