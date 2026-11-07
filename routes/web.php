<?php

use App\Models\BarangKeluar;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/admin/barang-keluar/{record}/pdf', function (int|string $record) {
        $data = BarangKeluar::with('items')->findOrFail($record);

        $safeFilename = 'Surat-Jalan-' . str_replace(['/', '\\', ' '], '-', $data->no_surat) . '.pdf';

        $pdf = Pdf::loadView('pdf.surat-jalan-barang-keluar', [
            'data' => $data,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream($safeFilename);
    })->name('barang-keluar.pdf');

    Route::get('/admin/kelola-invoice/{record}/pdf', function (int|string $record) {
        $invoice = Invoice::with('items')->findOrFail($record);

        $safeFilename = 'Sales-Invoice-' . str_replace(['/', '\\', ' '], '-', $invoice->no_invoice) . '.pdf';

        $pdf = Pdf::loadView('pdf.sales-invoice', [
            'invoice' => $invoice,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream($safeFilename);
    })->name('kelola-invoice.pdf');

    Route::get('/admin/kelola-invoice/{record}/excel', function (int|string $record) {
        $invoice = Invoice::with('items')->findOrFail($record);

        $safeFilename = 'Sales-Invoice-' . str_replace(['/', '\\', ' '], '-', $invoice->no_invoice) . '.xls';

        $content = view('excel.sales-invoice-excel', [
            'invoice' => $invoice,
        ])->render();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $safeFilename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    })->name('kelola-invoice.excel');
});
