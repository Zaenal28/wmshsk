<?php

namespace App\Filament\Pages;

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\BarangKeluarItem;
use App\Models\PerencanaanStok;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Livewire\WithFileUploads;
use OpenSpout\Reader\Common\Creator\ReaderEntityFactory;

class StokGudangPusat extends Page
{
    use WithFileUploads;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $navigationLabel = 'Stok Gudang Pusat';
    protected static ?string $title = 'Stok Gudang Pusat & Perencanaan Reorder';
    protected static ?string $navigationGroup = 'Gudang & Inventori';
    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'stok-gudang-pusat';

    protected static string $view = 'filament.pages.stok-gudang-pusat';

    // Filter controls
    public string $tipePeriode = 'bulanan'; // bulanan, mingguan, harian
    public int $selectedMonth = 11;
    public int $selectedYear = 2026;
    public ?string $filterKategori = '';
    public ?string $filterStatus = '';
    public ?string $search = '';

    // Modal Import
    public bool $showImportModal = false;
    public $fileImport = null;

    // Modal Edit
    public bool $showEditModal = false;
    public ?int $editingId = null;
    public ?string $editNamaBarang = '';
    public ?string $editKategori = '';
    public ?string $editTanggalPesan = null;
    public ?int $editStokPabrik = null;
    public ?string $editStatusPesan = 'Tidak';
    public ?string $editExpired1 = '';
    public ?string $editExpired2 = '';
    public ?string $editCatatan = '';

    public function mount(): void
    {
        $this->selectedYear = (int) (request('year') ?? date('Y'));
        $this->selectedMonth = (int) (request('month') ?? date('n'));
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        return in_array($user->role, ['gudang_pusat', 'superadmin']) || $user->email === 'gudangpusat@wms.com';
    }

    public function getPeriodeKey(): string
    {
        return sprintf('%04d-%02d', $this->selectedYear, $this->selectedMonth);
    }

    public function getPeriodeLabelProperty(): string
    {
        $namaBulan = [
            1 => 'JANUARI', 2 => 'FEBRUARI', 3 => 'MARET', 4 => 'APRIL',
            5 => 'MEI', 6 => 'JUNI', 7 => 'JULI', 8 => 'AGUSTUS',
            9 => 'SEPTEMBER', 10 => 'OKTOBER', 11 => 'NOVEMBER', 12 => 'DESEMBER',
        ];

        return ($namaBulan[$this->selectedMonth] ?? 'BULAN') . ' ' . $this->selectedYear;
    }

    public function getKategoriListProperty(): array
    {
        return array_values(Barang::getKategoriOptions());
    }

    public function getPerencanaanStoksGroupedProperty(): Collection
    {
        $periode = $this->getPeriodeKey();

        // Cek jika data untuk periode ini belum ada di database, otomatis generate langsung dari transaksi WMS!
        $count = PerencanaanStok::where('periode', $periode)
            ->where('tipe_periode', $this->tipePeriode)
            ->count();

        if ($count === 0) {
            $this->sinkronkanDariWms(silent: true);
        }

        $query = PerencanaanStok::query()
            ->where('periode', $periode)
            ->where('tipe_periode', $this->tipePeriode);

        if (!empty($this->filterKategori)) {
            $query->where('kategori', $this->filterKategori);
        }

        if (!empty($this->filterStatus)) {
            $query->where('status_pesan', $this->filterStatus);
        }

        if (!empty($this->search)) {
            $term = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('nama_barang', 'like', $term)
                  ->orWhere('no_urut', 'like', $term)
                  ->orWhere('kategori', 'like', $term);
            });
        }

        $items = $query->orderBy('id', 'asc')->get();

        // Kelompokkan per kategori untuk tampilan rapi seperti Excel
        return $items->groupBy(fn ($item) => $item->kategori ?: 'Lain-lain');
    }

    public function sinkronkanDariWms(bool $silent = false): void
    {
        $periode = $this->getPeriodeKey();
        $startDate = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Periode bulan lalu untuk acuan stok awal dan histori rata-rata
        $m1Start = $startDate->copy()->subMonth()->startOfMonth();
        $m1End = $m1Start->copy()->endOfMonth();
        $m1Key = $m1Start->format('Y-m');

        $m2Start = $startDate->copy()->subMonths(2)->startOfMonth();
        $m2End = $m2Start->copy()->endOfMonth();
        $m2Key = $m2Start->format('Y-m');

        $m3Start = $startDate->copy()->subMonths(3)->startOfMonth();
        $m3End = $m3Start->copy()->endOfMonth();
        $m3Key = $m3Start->format('Y-m');

        // Ambil daftar produk dari bulan sebelumnya dan dari master Barang
        $prevRecords = PerencanaanStok::where('periode', $m1Key)
            ->orderBy('id', 'asc')
            ->get()
            ->keyBy('nama_barang');

        $m2Records = PerencanaanStok::where('periode', $m2Key)
            ->get()
            ->keyBy('nama_barang');

        $m3Records = PerencanaanStok::where('periode', $m3Key)
            ->get()
            ->keyBy('nama_barang');

        $allBarangs = Barang::all()->keyBy('nama_barang');

        // Gabungkan semua nama produk unik dan standarkan
        $allProductNames = $prevRecords->keys()
            ->merge($allBarangs->keys())
            ->map(fn ($n) => PerencanaanStok::standardizeProductName($n))
            ->unique();

        $syncedCount = 0;

        foreach ($allProductNames as $namaBarang) {
            $barang = $allBarangs->get($namaBarang);
            $prevStok = $prevRecords->get($namaBarang);
            $m2Stok = $m2Records->get($namaBarang);
            $m3Stok = $m3Records->get($namaBarang);

            // 1. Hitung histori pengeluaran 1, 2, 3 bulan sebelumnya
            $keluarM1 = 0;
            $keluarM2 = 0;
            $keluarM3 = 0;

            if ($barang) {
                $keluarM1 = (int) BarangKeluarItem::where('barang_id', $barang->id)
                    ->whereHas('barangKeluar', fn ($q) => $q->whereBetween('tanggal_keluar', [$m1Start, $m1End]))
                    ->sum('jumlah');

                $keluarM2 = (int) BarangKeluarItem::where('barang_id', $barang->id)
                    ->whereHas('barangKeluar', fn ($q) => $q->whereBetween('tanggal_keluar', [$m2Start, $m2End]))
                    ->sum('jumlah');

                $keluarM3 = (int) BarangKeluarItem::where('barang_id', $barang->id)
                    ->whereHas('barangKeluar', fn ($q) => $q->whereBetween('tanggal_keluar', [$m3Start, $m3End]))
                    ->sum('jumlah');
            }

            // Jika di tabel barang_keluar kosong (karena periode historis lama), ambil dari total_keluar di perencanaan_stoks
            $k1 = $keluarM1 > 0 ? $keluarM1 : ($prevStok ? $prevStok->total_keluar : 0);
            $k2 = $keluarM2 > 0 ? $keluarM2 : ($m2Stok ? $m2Stok->total_keluar : 0);
            $k3 = $keluarM3 > 0 ? $keluarM3 : ($m3Stok ? $m3Stok->total_keluar : 0);

            // Jika k1, k2, k3 masih 0, fallback ke nilai rata-rata tersimpan di bulan lalu
            $rata1 = $k1 > 0 ? $k1 : ($prevStok ? $prevStok->rata_1_bulan : 0);
            $rata2 = ($k1 + $k2) > 0 ? ($k1 + $k2) : ($prevStok ? $prevStok->rata_2_bulan : 0);
            $rata3 = ($k1 + $k2 + $k3) > 0 ? ($k1 + $k2 + $k3) : ($prevStok ? $prevStok->rata_3_bulan : 0);

            // 2. Stok Awal: Otomatis membaca stok akhir dari bulan sebelumnya
            $stokAwal = $prevStok ? $prevStok->stok_akhir : ($barang ? $barang->jumlah : 0);

            // 3. Mutasi masuk bulan berjalan dari transaksi WMS
            $masukPabrik = 0;
            $masukCabang = 0;
            if ($barang) {
                $masukPabrik = (int) Barang::where('nama_barang', $barang->nama_barang)
                    ->whereBetween('tanggal_masuk', [$startDate, $endDate])
                    ->where(function ($q) {
                        $q->where('supplier', 'like', '%pabrik%')
                          ->orWhere('supplier', 'like', '%bio%')
                          ->orWhereNull('supplier');
                    })
                    ->sum('jumlah');

                $masukCabang = (int) Barang::where('nama_barang', $barang->nama_barang)
                    ->whereBetween('tanggal_masuk', [$startDate, $endDate])
                    ->where('supplier', 'like', '%cabang%')
                    ->sum('jumlah');
            }

            // 4. Mutasi pengeluaran bulan berjalan terinci per tujuan dari transaksi WMS
            $keluarAp = 0;
            $keluarRf = 0;
            $keluarMp = 0;
            $keluarPwk = 0;
            $keluarBdg = 0;
            $keluarRacik = 0;
            $returPabrik = 0;

            if ($barang) {
                $keluarAp = (int) BarangKeluarItem::where('barang_id', $barang->id)
                    ->whereHas('barangKeluar', fn ($q) => $q->whereBetween('tanggal_keluar', [$startDate, $endDate])->where('tujuan', 'like', '%apotek%'))
                    ->sum('jumlah');

                $keluarRf = (int) BarangKeluarItem::where('barang_id', $barang->id)
                    ->whereHas('barangKeluar', fn ($q) => $q->whereBetween('tanggal_keluar', [$startDate, $endDate])->where('tujuan', 'like', '%facial%'))
                    ->sum('jumlah');

                $keluarMp = (int) BarangKeluarItem::where('barang_id', $barang->id)
                    ->whereHas('barangKeluar', fn ($q) => $q->whereBetween('tanggal_keluar', [$startDate, $endDate])->where('tujuan', 'like', '%marketplace%'))
                    ->sum('jumlah');

                $keluarPwk = (int) BarangKeluarItem::where('barang_id', $barang->id)
                    ->whereHas('barangKeluar', fn ($q) => $q->whereBetween('tanggal_keluar', [$startDate, $endDate])->where('tujuan', 'like', '%purwakarta%'))
                    ->sum('jumlah');

                $keluarBdg = (int) BarangKeluarItem::where('barang_id', $barang->id)
                    ->whereHas('barangKeluar', fn ($q) => $q->whereBetween('tanggal_keluar', [$startDate, $endDate])->where('tujuan', 'like', '%bandung%'))
                    ->sum('jumlah');

                $keluarRacik = (int) BarangKeluarItem::where('barang_id', $barang->id)
                    ->whereHas('barangKeluar', fn ($q) => $q->whereBetween('tanggal_keluar', [$startDate, $endDate])->where('tujuan', 'like', '%racik%'))
                    ->sum('jumlah');

                $returPabrik = (int) BarangKeluarItem::where('barang_id', $barang->id)
                    ->whereHas('barangKeluar', fn ($q) => $q->whereBetween('tanggal_keluar', [$startDate, $endDate])->where('tujuan', 'like', '%retur%'))
                    ->sum('jumlah');
            }

            $totalKeluar = $keluarAp + $keluarRf + $keluarMp + $keluarPwk + $keluarBdg + $keluarRacik;
            $stokAkhir = ($stokAwal + $masukPabrik + $masukCabang) - $totalKeluar - $returPabrik;

            // 5. Cek apakah record sudah ada untuk mempertahankan custom input (tanggal pesan, status discontinue, stok pabrik)
            $existing = PerencanaanStok::where('periode', $periode)
                ->where('nama_barang', $namaBarang)
                ->first();

            $isManualPesan = (bool) ($existing?->is_manual_pesan ?? false);
            if ($isManualPesan && $existing?->status_pesan) {
                $statusPesan = $existing->status_pesan;
            } elseif ($existing?->status_pesan === 'Discontinue' || $prevStok?->status_pesan === 'Discontinue') {
                $statusPesan = 'Discontinue';
                $isManualPesan = true;
            } else {
                // Rumus Spreadsheet: =IF(stok_akhir < rata_1_bulan; "Pesan"; "Tidak")
                $statusPesan = ((int) $stokAkhir < (int) $rata1) ? 'Pesan' : 'Tidak';
            }

            $kategori = $existing?->kategori ?? ($prevStok?->kategori ?? ($barang?->kategori ?: 'Lain-lain'));
            $noUrut = $existing?->no_urut ?? ($prevStok?->no_urut ?? ($barang ? (string) $barang->no : null));
            $expired1 = $existing?->expired_1 ?? ($prevStok?->expired_1 ?? ($barang?->tanggal_expired ? Carbon::parse($barang->tanggal_expired)->format('n/Y') : null));
            $expired2 = $existing?->expired_2 ?? $prevStok?->expired_2;
            $stokPabrik = $existing?->stok_pabrik ?? $prevStok?->stok_pabrik;

            PerencanaanStok::updateOrCreate(
                [
                    'periode' => $periode,
                    'tipe_periode' => 'bulanan',
                    'nama_barang' => $namaBarang,
                ],
                [
                    'barang_id' => $barang?->id,
                    'no_urut' => $noUrut,
                    'kategori' => $kategori,
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
                    'status_pesan' => $statusPesan,
                    'is_manual_pesan' => $isManualPesan,
                    'stok_pabrik' => $stokPabrik,
                    'expired_1' => $expired1,
                    'expired_2' => $expired2,
                    'tanggal_mulai' => $startDate->toDateString(),
                    'tanggal_selesai' => $endDate->toDateString(),
                ]
            );

            $syncedCount++;
        }

        if (!$silent) {
            Notification::make()
                ->title("Sinkronisasi Berhasil!")
                ->body("{$syncedCount} produk berhasil disinkronkan dari data transaksi WMS untuk periode {$this->periode_label}.")
                ->success()
                ->send();
        }
    }

    public function bukaModalImport(): void
    {
        $this->fileImport = null;
        $this->showImportModal = true;
    }

    public function tutupModalImport(): void
    {
        $this->showImportModal = false;
    }

    public function prosesImportFile(): void
    {
        $this->validate([
            'fileImport' => 'required|file|max:10240',
        ], [
            'fileImport.required' => 'Pilih file CSV atau Excel (.xlsx) terlebih dahulu.',
        ]);

        $path = $this->fileImport->getRealPath();
        $ext = strtolower($this->fileImport->getClientOriginalExtension());
        $periode = $this->getPeriodeKey();

        $countImported = 0;
        $currentKategori = 'Lain-lain';

        if ($ext === 'csv' || $ext === 'txt') {
            $handle = fopen($path, 'r');
            if ($handle) {
                // Lewati baris header jika ada
                fgetcsv($handle);
                while (($row = fgetcsv($handle, 2000, ',')) !== false) {
                    if ($this->simpanBarisImport($row, $periode, $currentKategori)) {
                        $countImported++;
                    }
                }
                fclose($handle);
            }
        } elseif ($ext === 'xlsx') {
            $reader = ReaderEntityFactory::createXLSXReader();
            $reader->open($path);
            foreach ($reader->getSheetIterator() as $sheet) {
                $isHeader = true;
                foreach ($sheet->getRowIterator() as $rowObj) {
                    if ($isHeader) {
                        $isHeader = false;
                        continue;
                    }
                    $cells = array_map(fn ($c) => $c->getValue(), $rowObj->getCells());
                    if ($this->simpanBarisImport($cells, $periode, $currentKategori)) {
                        $countImported++;
                    }
                }
            }
            $reader->close();
        }

        $this->showImportModal = false;

        Notification::make()
            ->title("Import Berhasil!")
            ->body("{$countImported} data produk berhasil diimport ke periode {$this->periode_label}.")
            ->success()
            ->send();
    }

    protected function simpanBarisImport(array $row, string $periode, string &$currentKategori): bool
    {
        // Bersihkan data sel
        $row = array_map(fn ($val) => is_string($val) ? trim($val) : $val, $row);

        // Deteksi baris header kategori (misal sel 0 kosong, sel 1 berisi nama kategori)
        if (empty($row[0]) && !empty($row[1]) && empty($row[2])) {
            $currentKategori = $row[1];
            return false;
        }

        $namaBarang = $row[1] ?? null;
        if (empty($namaBarang)) {
            return false;
        }

        $namaBarang = PerencanaanStok::standardizeProductName($namaBarang);

        $noUrut = $row[0] ?? null;
        $rata3 = (int) ($row[2] ?? 0);
        $rata2 = (int) ($row[3] ?? 0);
        $rata1 = (int) ($row[4] ?? 0);
        $stokAwal = (int) ($row[5] ?? 0);
        $masukPabrik = (int) ($row[6] ?? 0);
        $masukCabang = (int) ($row[7] ?? 0);
        $keluarAp = (int) ($row[8] ?? 0);
        $keluarRf = (int) ($row[9] ?? 0);
        $keluarMp = (int) ($row[10] ?? 0);
        $keluarPwk = (int) ($row[11] ?? 0);
        $keluarBdg = (int) ($row[12] ?? 0);
        $keluarRacik = (int) ($row[13] ?? 0);
        $returPabrik = (int) ($row[15] ?? 0);

        $totalKeluar = $keluarAp + $keluarRf + $keluarMp + $keluarPwk + $keluarBdg + $keluarRacik;
        $stokAkhir = ($stokAwal + $masukPabrik + $masukCabang) - $totalKeluar - $returPabrik;

        $statusPesan = $row[18] ?? 'Tidak';
        if (empty($statusPesan)) {
            $statusPesan = ($rata3 > 0 && $stokAkhir < $rata3) ? 'Pesan' : 'Tidak';
        }

        $barang = Barang::where('nama_barang', 'like', "%{$namaBarang}%")->first();

        PerencanaanStok::updateOrCreate(
            [
                'periode' => $periode,
                'tipe_periode' => 'bulanan',
                'nama_barang' => $barang ? $barang->nama_barang : $namaBarang,
            ],
            [
                'barang_id' => $barang?->id,
                'no_urut' => $noUrut,
                'kategori' => $barang?->kategori ?: $currentKategori,
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
                'status_pesan' => $statusPesan,
                'tanggal_pesan' => !empty($row[19]) ? Carbon::parse($row[19]) : null,
                'stok_pabrik' => !empty($row[20]) ? (int) $row[20] : null,
                'expired_1' => $row[21] ?? null,
                'expired_2' => $row[22] ?? null,
            ]
        );

        return true;
    }

    public function bukaModalEdit(int $id): void
    {
        $record = PerencanaanStok::find($id);
        if (!$record) return;

        $this->editingId = $record->id;
        $this->editNamaBarang = $record->nama_barang;
        $this->editKategori = $record->kategori;
        $this->editTanggalPesan = $record->tanggal_pesan ? $record->tanggal_pesan->format('Y-m-d') : null;
        $this->editStokPabrik = $record->stok_pabrik;

        // Jika tidak diset manual dan bukan Discontinue, gunakan opsi Otomatis
        if (!$record->is_manual_pesan && $record->status_pesan !== 'Discontinue') {
            $this->editStatusPesan = 'Otomatis';
        } else {
            $this->editStatusPesan = $record->status_pesan;
        }

        $this->editExpired1 = $record->expired_1;
        $this->editExpired2 = $record->expired_2;
        $this->editCatatan = $record->catatan;

        $this->showEditModal = true;
    }

    public function tutupModalEdit(): void
    {
        $this->showEditModal = false;
        $this->editingId = null;
    }

    public function simpanModalEdit(): void
    {
        if (!$this->editingId) return;

        $record = PerencanaanStok::find($this->editingId);
        if ($record) {
            $isManual = false;
            $statusPesan = 'Tidak';

            if ($this->editStatusPesan === 'Otomatis') {
                $isManual = false;
                $statusPesan = ((int) $record->stok_akhir < (int) $record->rata_1_bulan) ? 'Pesan' : 'Tidak';
            } elseif ($this->editStatusPesan === 'Discontinue') {
                $isManual = true;
                $statusPesan = 'Discontinue';
            } elseif ($this->editStatusPesan === 'Pesan') {
                $isManual = true;
                $statusPesan = 'Pesan';
            } elseif ($this->editStatusPesan === 'Tidak') {
                $isManual = true;
                $statusPesan = 'Tidak';
            } else {
                $isManual = false;
                $statusPesan = ((int) $record->stok_akhir < (int) $record->rata_1_bulan) ? 'Pesan' : 'Tidak';
            }

            $record->update([
                'kategori' => $this->editKategori ?: 'Lain-lain',
                'tanggal_pesan' => $this->editTanggalPesan ?: null,
                'stok_pabrik' => $this->editStokPabrik !== null ? (int) $this->editStokPabrik : null,
                'status_pesan' => $statusPesan,
                'is_manual_pesan' => $isManual,
                'expired_1' => $this->editExpired1,
                'expired_2' => $this->editExpired2,
                'catatan' => $this->editCatatan,
            ]);

            // Jika ada master Barang terkait, sinkronkan juga kategorinya
            if ($record->barang_id) {
                Barang::where('id', $record->barang_id)->update([
                    'kategori' => $this->editKategori ?: 'Lain-lain',
                ]);
            }

            Notification::make()
                ->title("Data {$record->nama_barang} berhasil diperbarui!")
                ->body("Status pesan: {$statusPesan}" . ($isManual ? ' (Manual)' : ' (Otomatis Rumus)'))
                ->success()
                ->send();
        }

        $this->showEditModal = false;
    }

    public function updateStatusPesan(int $id, string $status): void
    {
        $record = PerencanaanStok::find($id);
        if ($record) {
            $isManual = ($status !== 'Otomatis');
            $finalStatus = $status;
            if ($status === 'Otomatis') {
                $finalStatus = ((int) $record->stok_akhir < (int) $record->rata_1_bulan) ? 'Pesan' : 'Tidak';
            }
            $record->update([
                'status_pesan' => $finalStatus,
                'is_manual_pesan' => $isManual,
            ]);
            Notification::make()
                ->title("Status {$record->nama_barang} diubah ke {$finalStatus}" . ($isManual ? ' (Manual)' : ' (Otomatis)'))
                ->success()
                ->send();
        }
    }

    public function exportExcel()
    {
        $periode = $this->getPeriodeKey();
        $title = "STOK GUDANG PUSAT " . $this->periode_label;
        $groups = $this->perencanaan_stoks_grouped;

        $content = view('excel.stok-gudang-pusat-excel', [
            'title' => $title,
            'periodeLabel' => $this->periode_label,
            'groups' => $groups,
        ])->render();

        $filename = 'Stok-Gudang-Pusat-' . str_replace(' ', '-', $this->periode_label) . '.xls';

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
