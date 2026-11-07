<?php

namespace App\Filament\Pages;

use App\Models\Barang;
use App\Models\BarangKeluarItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class EditInvoice extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static ?string $title = 'Edit Invoice';
    protected static ?string $slug = 'kelola-invoice/{record}/edit';
    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.tambah-invoice';

    public Invoice $invoiceModel;

    public string $tujuan = '';
    public string $nama_tempat = '';
    public string $no_invoice = '';
    public string $tanggal_invoice = '';
    public string $dari_tanggal = '';
    public string $ke_tanggal = '';

    public array $items = [];

    public function mount(int|string $record): void
    {
        $this->invoiceModel = Invoice::with('items')->findOrFail($record);

        $this->tujuan = $this->invoiceModel->tujuan;
        $this->nama_tempat = (string) $this->invoiceModel->nama_tempat;
        $this->no_invoice = $this->invoiceModel->no_invoice;
        $this->tanggal_invoice = $this->invoiceModel->tanggal_invoice?->format('Y-m-d') ?? now()->format('Y-m-d');
        $this->dari_tanggal = $this->invoiceModel->dari_tanggal?->format('Y-m-d') ?? now()->format('Y-m-d');
        $this->ke_tanggal = $this->invoiceModel->ke_tanggal?->format('Y-m-d') ?? now()->format('Y-m-d');

        $this->items = $this->invoiceModel->items->map(fn (InvoiceItem $item) => [
            'barang_keluar_item_id' => $item->barang_keluar_item_id,
            'barang_id' => $item->barang_id,
            'no_surat_jalan' => $item->no_surat_jalan,
            'tanggal_keluar' => $item->tanggal_keluar?->format('Y-m-d'),
            'item_description' => $item->item_description,
            'qty' => (int) $item->qty,
            'item_unit' => $item->item_unit,
            'unit_price' => (float) $item->unit_price,
            'amount' => (float) $item->amount,
        ])->toArray();
    }

    public function tarikDataBarangKeluar(): void
    {
        if (trim($this->tujuan) === '' || $this->dari_tanggal === '' || $this->ke_tanggal === '') {
            Notification::make()
                ->title('Pastikan Tujuan, Dari Tanggal, dan Ke Tanggal sudah diisi.')
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
            Notification::make()
                ->title("Tidak ditemukan transaksi Barang Keluar untuk tujuan '{$this->tujuan}' pada periode tersebut.")
                ->warning()
                ->send();
            return;
        }

        $tarikItems = [];
        foreach ($keluarItems as $row) {
            $masterBarang = $row->barang ?: Barang::where('no', $row->no_barang)->orWhere('nama_barang', $row->nama_barang)->first();
            $unit = strtolower($masterBarang?->satuan ?: 'pcs');
            $unitPrice = (float) ($masterBarang?->harga_barang ?? 0);
            $qty = (int) $row->jumlah;

            $tarikItems[] = [
                'barang_keluar_item_id' => $row->id,
                'barang_id' => $masterBarang?->id,
                'no_surat_jalan' => $row->barangKeluar?->no_surat,
                'tanggal_keluar' => $row->barangKeluar?->tanggal_keluar?->format('Y-m-d'),
                'item_description' => $row->nama_barang,
                'qty' => $qty,
                'item_unit' => $unit,
                'unit_price' => $unitPrice,
                'amount' => $qty * $unitPrice,
            ];
        }

        $this->items = $tarikItems;

        Notification::make()
            ->title('Berhasil menarik ulang ' . count($tarikItems) . ' item barang keluar!')
            ->success()
            ->send();
    }

    public function updatedItems(): void
    {
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
        ]);

        if (empty($this->items)) {
            Notification::make()
                ->title('Daftar barang pada invoice tidak boleh kosong.')
                ->warning()
                ->send();
            return;
        }

        DB::transaction(function () {
            $this->invoiceModel->update([
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

            $this->invoiceModel->items()->delete();

            foreach ($this->items as $row) {
                $qty = (int) ($row['qty'] ?? 0);
                $price = (float) ($row['unit_price'] ?? 0);

                InvoiceItem::create([
                    'invoice_id' => $this->invoiceModel->id,
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
            ->title('Invoice berhasil diperbarui!')
            ->success()
            ->send();

        $this->redirect(KelolaInvoice::getUrl(), navigate: true);
    }
}
