<?php

namespace App\Filament\Pages;

use App\Models\Barang;
use App\Models\BarangKeluar as BarangKeluarModel;
use App\Models\BarangKeluarItem;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class EditBarangKeluar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static ?string $title = 'Edit Barang Keluar';
    protected static ?string $slug = 'barang-keluar/{record}/edit';
    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.tambah-barang-keluar';

    public BarangKeluarModel $recordModel;

    public string $tanggal_keluar = '';
    public string $no_surat = '';
    public string $pic = '';
    public string $tujuan = '';
    public string $nama_penerima = '';

    public ?int $selected_barang_id = null;
    public string $input_no_barang = '';
    public string $input_nama_barang = '';
    public string $input_expired = '';
    public int|string $input_jumlah = '';
    public int $stok_tersedia = 0;

    public bool $showModalBarang = false;
    public string $searchModal = '';

    public array $items = [];

    public function mount(int|string $record): void
    {
        $this->recordModel = BarangKeluarModel::with('items')->findOrFail($record);

        $this->tanggal_keluar = $this->recordModel->tanggal_keluar?->format('Y-m-d') ?? now()->format('Y-m-d');
        $this->no_surat = $this->recordModel->no_surat;
        $this->pic = $this->recordModel->pic;
        $this->tujuan = $this->recordModel->tujuan;
        $this->nama_penerima = (string) $this->recordModel->nama_penerima;

        $this->items = $this->recordModel->items->map(fn (BarangKeluarItem $item) => [
            'barang_id' => $item->barang_id,
            'no_barang' => $item->no_barang,
            'nama_barang' => $item->nama_barang,
            'tanggal_expired' => $item->tanggal_expired?->format('Y-m-d'),
            'jumlah' => $item->jumlah,
        ])->toArray();
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
        $this->selected_barang_id = null;
        $this->input_no_barang = '';
        $this->input_nama_barang = '';
        $this->input_expired = '';
        $this->input_jumlah = '';
        $this->stok_tersedia = 0;
    }

    public function tambahItemKeTabel(): void
    {
        if (trim($this->input_nama_barang) === '' && ! $this->selected_barang_id) {
            Notification::make()
                ->title('Pilih barang terlebih dahulu.')
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

        $this->items[] = [
            'barang_id' => $this->selected_barang_id,
            'no_barang' => $this->input_no_barang ?: '-',
            'nama_barang' => $this->input_nama_barang,
            'tanggal_expired' => $this->input_expired ?: null,
            'jumlah' => $qty,
        ];

        $this->resetItemInput();
    }

    public function hapusItemDariTabel(int $index): void
    {
        if (isset($this->items[$index])) {
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
        ]);

        if (empty($this->items)) {
            Notification::make()
                ->title('Tambahkan minimal 1 barang ke daftar sebelum menyimpan.')
                ->warning()
                ->send();
            return;
        }

        DB::transaction(function () {
            // Kembalikan stok lama terlebih dahulu
            foreach ($this->recordModel->items as $oldItem) {
                if ($oldItem->barang_id) {
                    Barang::where('id', $oldItem->barang_id)->increment('jumlah', $oldItem->jumlah);
                }
            }

            $this->recordModel->items()->delete();

            $totalQty = collect($this->items)->sum('jumlah');

            $this->recordModel->update([
                'tanggal_keluar' => $this->tanggal_keluar,
                'no_surat' => $this->no_surat,
                'pic' => $this->pic,
                'tujuan' => $this->tujuan,
                'nama_penerima' => $this->nama_penerima,
                'total_jumlah' => $totalQty,
            ]);

            foreach ($this->items as $row) {
                BarangKeluarItem::create([
                    'barang_keluar_id' => $this->recordModel->id,
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
            ->title('Data Barang Keluar berhasil diperbarui!')
            ->success()
            ->send();

        $this->redirect(BarangKeluar::getUrl(), navigate: true);
    }
}
