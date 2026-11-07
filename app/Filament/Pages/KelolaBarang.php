<?php

namespace App\Filament\Pages;

use App\Models\Barang;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class KelolaBarang extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Kelola Barang';
    protected static ?string $title = 'Kelola Barang';
    protected static ?string $navigationGroup = 'Gudang & Inventori';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'kelola-barang';

    protected static string $view = 'filament.pages.kelola-barang';

    public static function getNavigationBadge(): ?string
    {
        return (string) Barang::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public function getBarangsProperty()
    {
        return Barang::query()
            ->select([
                'id',
                'no',
                'nama_barang',
                'jumlah',
                'tanggal_masuk',
                'tanggal_expired',
            ])
            ->orderBy('no', 'asc')
            ->get();
    }

    public function hapusBarang(int $id): void
    {
        $barang = Barang::find($id);

        if ($barang) {
            $nama = $barang->nama_barang;
            $barang->delete();

            Notification::make()
                ->title("Barang '{$nama}' berhasil dihapus.")
                ->success()
                ->send();
        }

        $this->redirect(static::getUrl(), navigate: true);
    }
}
