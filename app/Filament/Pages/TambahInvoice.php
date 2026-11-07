<?php

namespace App\Filament\Pages;

use App\Models\Barang;
use App\Models\BarangKeluarItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class TambahInvoice extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-plus-circle';
    protected static ?string $title = 'Tambah Invoice';
    protected static ?string $slug = 'kelola-invoice/tambah';
    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.tambah-invoice';

    public string $tujuan = '';
    public string $nama_tempat = '';
    public string $no_invoice = '';
    public string $tanggal_invoice = '';
    public string $dari_tanggal = '';
    public string $ke_tanggal = '';

    public array $items = [];

    public function mount(): void
    {
        $this->tanggal_invoice = now()->format('Y-m-d');
        $this->dari_tanggal = now()->startOfMonth()->format('Y-m-d');
        $this->ke_tanggal = now()->format('Y-m-d');

        $urut = str_pad((string) (Invoice::count() + 1), 3, '0', STR_PAD_LEFT);
        $this->no_invoice = 'INV/' . now()->format('y') . '/HBA-V/' . $urut;
    }

    public function tarikDataBarangKeluar(): void
    {
        if (trim($this->tujuan) === '') {
            Notification::make()
                ->title('Pilih Tujuan terlebih dahulu sebelum menarik data barang keluar.')
                ->warning()
                ->send();
            return;
        }

        if ($this->dari_tanggal === '' || $this->ke_tanggal === '') {
            Notification::make()
                ->title('Isi periode Dari Tanggal dan Ke Tanggal terlebih dahulu.')
                ->warning()
                ->send();
            return;
        }

        $keluarItems = BarangKeluarItem::query()
            ->with(['barangKeluar', 'barang'])
            ->whereHas('barangKeluar', function ($query) {
                $query->where('tujuan', $this->tujuan)
                    ->whereBetween('tanggal_keluar', [$this->dari_tanggal, $this->ke_tanggal]);
            })
            ->get();

        if ($keluarItems->isEmpty()) {
            $this->items = [];
            Notification::make()
                ->title("Tidak ditemukan transaksi Barang Keluar untuk tujuan '{$this->tujuan}' pada periode tersebut.")
                ->warning()
                ->send();
            return;
        }

        $tarikItems = [];

        foreach ($keluarItems as $row) {
            $masterBarang = $row->barang;
            if (! $masterBarang) {
                $masterBarang = Barang::where('no', $row->no_barang)
                    ->orWhere('nama_barang', $row->nama_barang)
                    ->first();
            }

            $unit = strtolower($masterBarang?->satuan ?: 'pcs');
            $unitPrice = (float) ($masterBarang?->harga_barang ?? 0);
            $qty = (int) $row->jumlah;
            $amount = $qty * $unitPrice;

            $tarikItems[] = [
                'barang_keluar_item_id' => $row->id,
                'barang_id' => $masterBarang?->id,
                'no_surat_jalan' => $row->barangKeluar?->no_surat,
                'tanggal_keluar' => $row->barangKeluar?->tanggal_keluar?->format('Y-m-d'),
                'item_description' => $row->nama_barang,
                'qty' => $qty,
                'item_unit' => $unit,
                'unit_price' => $unitPrice,
                'amount' => $amount,
            ];
        }

        $this->items = $tarikItems;

        Notification::make()
            ->title('Berhasil menarik ' . count($tarikItems) . ' item barang keluar!')
            ->success()
            ->send();
    }

    public function updatedItems($value, $key): void
    {
        // Re-kalkulasi otomatis Amount = QTY * Unit Price jika diedit di tabel
        foreach ($this->items as $idx => $item) {
            $qty = (int) ($item['qty'] ?? 0);
            $price = (float) ($item['unit_price'] ?? 0);
            $this->items[$idx]['amount'] = $qty * $price;
        }
    }

    public function hapusBarisItem(int $index): void
    {
        if (isset($this->items[$index])) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }

    public function getSubTotalProperty(): float
    {
        return (float) collect($this->items)->sum(fn ($i) => (float) ($i['amount'] ?? 0));
    }

    public function getDiskonNominalProperty(): float
    {
        return round($this->subTotal * 0.20, 2);
    }

    public function getTotalTagihanProperty(): float
    {
        return round($this->subTotal - $this->diskonNominal, 2);
    }

    public function simpanInvoice(): void
    {
        $this->validate([
            'tujuan' => 'required|string|max:150',
            'nama_tempat' => 'required|string|max:200',
            'no_invoice' => 'required|string|max:100',
            'dari_tanggal' => 'required|date',
            'ke_tanggal' => 'required|date',
        ], [
            'tujuan.required' => 'Tujuan wajib dipilih.',
            'nama_tempat.required' => 'Nama Tempat / Alamat wajib diisi.',
            'no_invoice.required' => 'No Invoice wajib diisi.',
            'dari_tanggal.required' => 'Dari tanggal wajib diisi.',
            'ke_tanggal.required' => 'Ke tanggal wajib diisi.',
        ]);

        if (empty($this->items)) {
            Notification::make()
                ->title('Klik tombol "Tarik Data Barang Keluar" terlebih dahulu hingga daftar barang tampil.')
                ->warning()
                ->send();
            return;
        }

        DB::transaction(function () {
            $invoice = Invoice::create([
                'no_invoice' => $this->no_invoice,
                'tanggal_invoice' => $this->tanggal_invoice ?: now()->format('Y-m-d'),
                'tujuan' => $this->tujuan,
                'nama_tempat' => $this->nama_tempat,
                'dari_tanggal' => $this->dari_tanggal,
                'ke_tanggal' => $this->ke_tanggal,
                'sub_total' => $this->subTotal,
                'diskon_persen' => 20,
                'diskon_nominal' => $this->diskonNominal,
                'total_tagihan' => $this->totalTagihan,
            ]);

            foreach ($this->items as $row) {
                $qty = (int) ($row['qty'] ?? 0);
                $price = (float) ($row['unit_price'] ?? 0);

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'barang_keluar_item_id' => $row['barang_keluar_item_id'] ?? null,
                    'barang_id' => $row['barang_id'] ?? null,
                    'no_surat_jalan' => $row['no_surat_jalan'] ?? null,
                    'tanggal_keluar' => $row['tanggal_keluar'] ?? null,
                    'item_description' => $row['item_description'],
                    'qty' => $qty,
                    'item_unit' => $row['item_unit'] ?: 'pcs',
                    'unit_price' => $price,
                    'amount' => $qty * $price,
                ]);
            }
        });

        Notification::make()
            ->title('Data Invoice berhasil disimpan!')
            ->success()
            ->send();

        $this->redirect(KelolaInvoice::getUrl(), navigate: true);
    }
}
