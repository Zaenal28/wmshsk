<?php

namespace App\Filament\Pages;

use App\Models\Barang;
use App\Models\PermintaanBarang as PermintaanBarangModel;
use App\Models\PermintaanBarangItem;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class TambahPermintaan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-plus-circle';
    protected static ?string $title = 'Tambah Permintaan Barang (Manual)';
    protected static ?string $slug = 'kelola-permintaan/tambah';
    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.tambah-permintaan';

    // Form Header Permintaan
    public string $tanggal_permintaan = '';
    public string $no_surat = '';
    public string $cabang = '';
    public string $pic = '';

    // Form Input Item Barang
    public ?int $selected_barang_id = null;
    public string $input_nama_barang = '';
    public int|string $input_jumlah = '';
    public string $input_keterangan = '';

    // Indeks item yang sedang diedit (-1 jika mode tambah baru)
    public int $editingIndex = -1;

    // Modal Pop-up Pencarian Barang
    public bool $showModalBarang = false;
    public string $searchModal = '';

    // Daftar Keranjang Item Barang
    public array $items = [];

    public function mount(): void
    {
        $this->tanggal_permintaan = now()->format('Y-m-d');
        $this->no_surat = PermintaanBarangModel::generateNoSurat($this->tanggal_permintaan);
        $this->pic = auth()->user()->name ?? 'ADMIN GUDANG';
    }

    public function updatedTanggalPermintaan($value): void
    {
        // Regenerate no_surat sesuai tanggal yang dipilih
        if ($value) {
            $this->no_surat = PermintaanBarangModel::generateNoSurat($value);
        }
    }

    public function getDaftarBarangProperty()
    {
        return Barang::query()
            ->when($this->searchModal !== '', function ($q) {
                $term = '%' . trim($this->searchModal) . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('no', 'like', $term)
                        ->orWhere('nama_barang', 'like', $term)
                        ->orWhere('kategori', 'like', $term);
                });
            })
            ->orderBy('nama_barang', 'asc')
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
            $this->selected_barang_id = $barang->id;
            $this->input_nama_barang = (string) $barang->nama_barang;
            if ($this->input_jumlah === '' || (int) $this->input_jumlah <= 0) {
                $this->input_jumlah = 1;
            }
        }
    }

    public function bukaModalBarang(): void
    {
        $this->searchModal = trim($this->input_nama_barang);
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
            $this->selected_barang_id = $barang->id;
            $this->input_nama_barang = (string) $barang->nama_barang;
            if ($this->input_jumlah === '' || (int) $this->input_jumlah <= 0) {
                $this->input_jumlah = 1;
            }
        }
        $this->showModalBarang = false;
    }

    public function resetItemInput(): void
    {
        $this->editingIndex = -1;
        $this->selected_barang_id = null;
        $this->input_nama_barang = '';
        $this->input_jumlah = '';
        $this->input_keterangan = '';
    }

    public function editItemDariTabel(int $index): void
    {
        if (isset($this->items[$index])) {
            $this->editingIndex = $index;
            $this->selected_barang_id = $this->items[$index]['barang_id'] ?? null;
            $this->input_nama_barang = $this->items[$index]['nama_barang'] ?? '';
            $this->input_jumlah = $this->items[$index]['jumlah'] ?? '';
            $this->input_keterangan = $this->items[$index]['keterangan'] ?? '';
        }
    }

    public function tambahItemKeTabel(): void
    {
        $namaBarang = trim($this->input_nama_barang);
        if ($namaBarang === '') {
            Notification::make()
                ->title('Nama barang wajib diisi atau dipilih dari daftar.')
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

        $payload = [
            'barang_id' => $this->selected_barang_id,
            'nama_barang' => $namaBarang,
            'jumlah' => $qty,
            'keterangan' => trim($this->input_keterangan),
        ];

        if ($this->editingIndex >= 0 && isset($this->items[$this->editingIndex])) {
            $this->items[$this->editingIndex] = array_merge($this->items[$this->editingIndex], $payload);
            Notification::make()
                ->title('Item barang berhasil diperbarui di tabel.')
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

    public function simpanPermintaan(): void
    {
        $this->validate([
            'tanggal_permintaan' => 'required|date',
            'no_surat' => 'required|string|max:100|unique:permintaan_barangs,no_surat',
            'cabang' => 'required|string|max:150',
            'pic' => 'required|string|max:100',
        ], [
            'tanggal_permintaan.required' => 'Tanggal permintaan wajib diisi.',
            'no_surat.required' => 'No Surat wajib diisi.',
            'no_surat.unique' => 'No Surat sudah pernah digunakan.',
            'cabang.required' => 'Cabang wajib dipilih atau diisi.',
            'pic.required' => 'PIC wajib diisi.',
        ]);

        if (empty($this->items)) {
            Notification::make()
                ->title('Tambahkan minimal 1 barang ke tabel barang sebelum menyimpan.')
                ->warning()
                ->send();
            return;
        }

        DB::transaction(function () {
            $totalJumlah = collect($this->items)->sum('jumlah');

            $permintaan = PermintaanBarangModel::create([
                'no_surat' => $this->no_surat,
                'tanggal_permintaan' => $this->tanggal_permintaan,
                'cabang' => $this->cabang,
                'pic' => $this->pic,
                'jumlah_barang' => $totalJumlah,
                'status' => 'Menunggu',
            ]);

            foreach ($this->items as $item) {
                PermintaanBarangItem::create([
                    'permintaan_barang_id' => $permintaan->id,
                    'barang_id' => $item['barang_id'] ?? null,
                    'nama_barang' => $item['nama_barang'],
                    'jumlah' => (int) $item['jumlah'],
                    'satuan' => 'pcs',
                    'catatan' => $item['keterangan'] ?? null,
                ]);
            }
        });

        Notification::make()
            ->title('Surat Permintaan Barang berhasil disimpan!')
            ->success()
            ->send();

        $this->redirect(KelolaPermintaan::getUrl(), navigate: true);
    }
}
