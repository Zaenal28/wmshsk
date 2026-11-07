<?php

namespace App\Filament\Pages;

use App\Models\BarangKeluar as BarangKeluarModel;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class BarangKeluar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';
    protected static ?string $navigationLabel = 'Barang Keluar';
    protected static ?string $title = 'Barang Keluar';
    protected static ?string $navigationGroup = 'Gudang & Inventori';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'barang-keluar';

    protected static string $view = 'filament.pages.barang-keluar';

    public static function getNavigationBadge(): ?string
    {
        return (string) BarangKeluarModel::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public function getBarangKeluarsProperty()
    {
        return BarangKeluarModel::query()
            ->with('items')
            ->orderBy('tanggal_keluar', 'desc')
            ->orderBy('id', 'desc')
            ->get();
    }

    public function hapusBarangKeluar(int $id): void
    {
        $record = BarangKeluarModel::with('items')->find($id);

        if ($record) {
            // Kembalikan stok barang ke master barang saat data barang keluar dihapus
            foreach ($record->items as $item) {
                if ($item->barang_id) {
                    \App\Models\Barang::where('id', $item->barang_id)->increment('jumlah', $item->jumlah);
                }
            }

            $noSurat = $record->no_surat;
            $record->delete();

            Notification::make()
                ->title("Data Barang Keluar '{$noSurat}' berhasil dihapus.")
                ->success()
                ->send();
        }

        $this->redirect(static::getUrl(), navigate: true);
    }
}
