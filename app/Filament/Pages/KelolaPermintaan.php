<?php

namespace App\Filament\Pages;

use App\Models\PermintaanBarang as PermintaanBarangModel;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class KelolaPermintaan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Kelola Permintaan';
    protected static ?string $title = 'Kelola Permintaan Barang';
    protected static ?string $navigationGroup = 'Transaksi & Dokumen';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'kelola-permintaan';

    protected static string $view = 'filament.pages.kelola-permintaan';

    public static function getNavigationBadge(): ?string
    {
        $count = PermintaanBarangModel::count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public function getPermintaanListProperty()
    {
        return PermintaanBarangModel::query()
            ->with('items')
            ->orderBy('tanggal_permintaan', 'desc')
            ->orderBy('id', 'desc')
            ->get();
    }

    public function hapusPermintaan(int $id): void
    {
        $record = PermintaanBarangModel::find($id);

        if ($record) {
            $noSurat = $record->no_surat;
            $record->delete();

            Notification::make()
                ->title("Data Permintaan '{$noSurat}' berhasil dihapus.")
                ->success()
                ->send();
        }

        $this->redirect(static::getUrl(), navigate: true);
    }
}
