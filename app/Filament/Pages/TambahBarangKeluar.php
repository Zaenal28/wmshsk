<?php

namespace App\Filament\Pages;

use App\Models\Barang;
use App\Models\BarangKeluar as BarangKeluarModel;
use App\Models\BarangKeluarItem;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class TambahBarangKeluar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-plus-circle';
    protected static ?string $title = 'Tambah Barang Keluar';
    protected static ?string $slug = 'barang-keluar/tambah';
    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.tambah-barang-keluar';

    // Form Header Surat Jalan
    public string $tanggal_keluar = '';
    public string $no_surat = '';
    public string $pic = '';
    public string $tujuan = '';
    public string $nama_penerima = '';

    // Form Input Item Barang (Referensi Gambar 1)
    public ?int $selected_barang_id = null;
    public string $input_no_barang = '';
    public string $input_nama_barang = '';
    public string $input_expired = '';
    public int|string $input_jumlah = '';
    public int $stok_tersedia = 0;

    // Indeks item yang sedang diedit (-1 jika mode tambah baru)
    public int $editingIndex = -1;

    // Modal Pop-up Pencarian Barang (Referensi Gambar 2)
    public bool $showModalBarang = false;
    public string $searchModal = '';

    // Daftar Keranjang Item Barang Keluar
    public array $items = [];

    public function mount(): void
    {
        $this->tanggal_keluar = now()->format('Y-m-d');
        $romanMonths = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];
        $bulanRomawi = $romanMonths[(int) now()->format('n')] ?? 'X';
        $urut = 2710 + BarangKeluarModel::count();

        $this->no_surat = "{$urut} /HSKBIO/ {$bulanRomawi} /" . now()->format('Y');
        $this->pic = 'RAHMA';
    }

    public function getDaftarBarangProperty()
    {
        return Barang::query()
            ->whereIn('status_racikan', ['Non Racikan', 'Sudah Diracik'])
            ->when($this->searchModal !== '', function ($q) {
                $term = '%' . trim($this->searchModal) . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('no', 'like', $term)
                        ->orWhere('nama_barang', 'like', $term)
                        ->orWhere('kategori', 'like', $term);
                });
            })
            ->orderBy('no', 'asc')
            ->limit(200)
            ->get();
    }

    public function updatedSelectedBarangId($value): void
    {
        if (! $value) {
            $this->resetItemInput();
            return;
        }

        $barang = Barang::find($value);
        if ($barang) {
            $this->isiDataBarang($barang);
        }
    }

    public function cariByNoBarang(): void
    {
        $kode = trim($this->input_no_barang);
        if ($kode !== '') {
            $barang = Barang::where('no', $kode)->first();
            if ($barang) {
                $this->isiDataBarang($barang);
                return;
            }
        }

        $this->searchModal = $kode;
        $this->showModalBarang = true;
    }

    public function bukaModalBarang(): void
    {
        $this->searchModal = trim($this->input_no_barang);
        $this->showModalBarang = true;
    }

    public function tutupModalBarang(): void
    {
        $this->showModalBarang = false;
    }

    public function pilihBarangDariModal(int $barangId): void
    {
        $barang = Barang::find($barangId);
        if ($barang) {
            $this->isiDataBarang($barang);
        }
        $this->showModalBarang = false;
    }

    protected function isiDataBarang(Barang $barang): void
    {
        $this->selected_barang_id = $barang->id;
        $this->input_no_barang = (string) $barang->no;
        $this->input_nama_barang = (string) $barang->nama_barang;
        $this->input_expired = $barang->tanggal_expired ? $barang->tanggal_expired->format('Y-m-d') : '';
        $this->stok_tersedia = (int) $barang->jumlah;
        if ($this->input_jumlah === '' || (int) $this->input_jumlah <= 0) {
            $this->input_jumlah = 1;
        }
    }

    public function resetItemInput(): void
    {
        $this->editingIndex = -1;
        $this->selected_barang_id = null;
        $this->input_no_barang = '';
        $this->input_nama_barang = '';
        $this->input_expired = '';
        $this->input_jumlah = '';
        $this->stok_tersedia = 0;
    }

    public function editItemDariTabel(int $index): void
    {
        if (isset($this->items[$index])) {
            $this->editingIndex = $index;
            $this->selected_barang_id = $this->items[$index]['barang_id'] ?? null;
            $this->input_no_barang = (string) ($this->items[$index]['no_barang'] ?? '');
            $this->input_nama_barang = (string) ($this->items[$index]['nama_barang'] ?? '');
            $this->input_expired = (string) ($this->items[$index]['tanggal_expired'] ?? '');
            $this->input_jumlah = $this->items[$index]['jumlah'] ?? '';

            if ($this->selected_barang_id) {
                $barang = Barang::find($this->selected_barang_id);
                $this->stok_tersedia = $barang ? (int) $barang->jumlah : 0;
            } else {
                $this->stok_tersedia = 0;
            }
        }
    }

    public function tambahItemKeTabel(): void
    {
        if (trim($this->input_nama_barang) === '' && ! $this->selected_barang_id) {
            Notification::make()
                ->title('Pilih barang terlebih dahulu melalui dropdown atau tombol cari (No Barang / SKU).')
                ->warning()
                ->send();
            return;
        }

        $qty = (int) $this->input_jumlah;
        if ($qty <= 0) {
            Notification::make()
                ->title('Jumlah barang harus lebih dari 0.')
                ->warning()
                ->send();
            return;
        }

        if ($this->selected_barang_id) {
            $barang = Barang::find($this->selected_barang_id);
            if ($barang && $qty > $barang->jumlah) {
                Notification::make()
                    ->title("Stok '{$barang->nama_barang}' tidak mencukupi (Sisa: {$barang->jumlah}).")
                    ->danger()
                    ->send();
                return;
            }
        }

        $payload = [
            'barang_id' => $this->selected_barang_id,
            'no_barang' => $this->input_no_barang ?: '-',
            'nama_barang' => $this->input_nama_barang,
            'tanggal_expired' => $this->input_expired ?: null,
            'jumlah' => $qty,
        ];

        if ($this->editingIndex >= 0 && isset($this->items[$this->editingIndex])) {
            $this->items[$this->editingIndex] = array_merge($this->items[$this->editingIndex], $payload);
            Notification::make()
                ->title('Item barang berhasil diperbarui di daftar.')
                ->success()
                ->send();
        } else {
            $this->items[] = $payload;
        }

        $this->resetItemInput();
    }

    public function hapusItemDariTabel(int $index): void
    {
        if (isset($this->items[$index])) {
            if ($this->editingIndex === $index) {
                $this->resetItemInput();
            } elseif ($this->editingIndex > $index) {
                $this->editingIndex--;
            }
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }

    public function simpanTransaksi(): void
    {
        $this->validate([
            'tanggal_keluar' => 'required|date',
            'no_surat' => 'required|string|max:100',
            'pic' => 'required|string|max:100',
            'tujuan' => 'required|string|max:150',
            'nama_penerima' => 'required|string|max:100',
        ], [
            'tanggal_keluar.required' => 'Tanggal keluar wajib diisi.',
            'no_surat.required' => 'No Surat wajib diisi.',
            'pic.required' => 'PIC wajib diisi.',
            'tujuan.required' => 'Tujuan wajib diisi.',
            'nama_penerima.required' => 'Nama Penerima wajib diisi.',
        ]);

        if (empty($this->items)) {
            Notification::make()
                ->title('Tambahkan minimal 1 barang ke daftar sebelum menyimpan.')
                ->warning()
                ->send();
            return;
        }

        DB::transaction(function () {
            $totalQty = collect($this->items)->sum('jumlah');

            $header = BarangKeluarModel::create([
                'tanggal_keluar' => $this->tanggal_keluar,
                'no_surat' => $this->no_surat,
                'pic' => $this->pic,
                'tujuan' => $this->tujuan,
                'nama_penerima' => $this->nama_penerima,
                'total_jumlah' => $totalQty,
            ]);

            foreach ($this->items as $row) {
                BarangKeluarItem::create([
                    'barang_keluar_id' => $header->id,
                    'barang_id' => $row['barang_id'] ?? null,
                    'no_barang' => $row['no_barang'],
                    'nama_barang' => $row['nama_barang'],
                    'tanggal_expired' => $row['tanggal_expired'] ?: null,
                    'jumlah' => (int) $row['jumlah'],
                ]);

                if (! empty($row['barang_id'])) {
                    Barang::where('id', $row['barang_id'])
                        ->where('jumlah', '>=', (int) $row['jumlah'])
                        ->decrement('jumlah', (int) $row['jumlah']);
                }
            }
        });

        Notification::make()
            ->title('Transaksi Barang Keluar berhasil disimpan!')
            ->success()
            ->send();

        $this->redirect(BarangKeluar::getUrl(), navigate: true);
    }
}
