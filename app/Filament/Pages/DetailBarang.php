<?php

namespace App\Filament\Pages;

use App\Models\Barang;
use App\Models\BarangKeluarItem;
use Filament\Pages\Page;

class DetailBarang extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-eye';
    protected static ?string $title = 'Detail Data Barang';
    protected static ?string $slug = 'kelola-barang/{record}/detail';

    // Sembunyikan dari sidebar karena diakses melalui tombol "Detail" di tabel Kelola Barang
    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.detail-barang';

    public Barang $barang;

    public function mount(int|string $record): void
    {
        $this->barang = Barang::findOrFail($record);
    }

    public function getTitle(): string
    {
        return 'Detail Barang: ' . $this->barang->nama_barang;
    }

    public function getRiwayatKeluarProperty()
    {
        return BarangKeluarItem::query()
            ->with('barangKeluar')
            ->where(function ($query) {
                $query->where('barang_id', $this->barang->id)
                    ->orWhere('no_barang', $this->barang->no)
                    ->orWhere('nama_barang', $this->barang->nama_barang);
            })
            ->whereHas('barangKeluar')
            ->latest('id')
            ->get();
    }
}
