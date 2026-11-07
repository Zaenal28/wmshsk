<?php

namespace App\Filament\Pages;

use App\Models\Invoice;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class KelolaInvoice extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Kelola Invoice';
    protected static ?string $title = 'Kelola Invoice';
    protected static ?string $navigationGroup = 'Transaksi & Dokumen';
    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'kelola-invoice';

    protected static string $view = 'filament.pages.kelola-invoice';

    public static function getNavigationBadge(): ?string
    {
        return (string) Invoice::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public function getInvoicesProperty()
    {
        return Invoice::query()
            ->orderBy('tanggal_invoice', 'desc')
            ->orderBy('id', 'desc')
            ->get();
    }

    public function hapusInvoice(int $id): void
    {
        $invoice = Invoice::find($id);

        if ($invoice) {
            $noInv = $invoice->no_invoice;
            $invoice->delete();

            Notification::make()
                ->title("Invoice '{$noInv}' berhasil dihapus.")
                ->success()
                ->send();
        }

        $this->redirect(static::getUrl(), navigate: true);
    }
}
