<?php

namespace App\Filament\Pages;

use App\Models\Invoice;
use Filament\Pages\Page;

class DetailInvoice extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-eye';
    protected static ?string $title = 'Detail Invoice';
    protected static ?string $slug = 'kelola-invoice/{record}/detail';
    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.detail-invoice';

    public Invoice $invoice;

    public function mount(int|string $record): void
    {
        $this->invoice = Invoice::with('items')->findOrFail($record);
    }

    public function getTitle(): string
    {
        return 'Detail Invoice: ' . $this->invoice->no_invoice;
    }
}
