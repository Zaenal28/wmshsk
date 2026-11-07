<x-filament-panels::page>
    @include('filament.components.datatables-assets')

    <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden antialiased">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1">
                <h3 class="text-base font-bold tracking-tight text-gray-900 dark:text-white">
                    Daftar Sales Invoice & Tagihan
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Kelola dokumen Sales Invoice berdasarkan penarikan data Barang Keluar per Tujuan dan Periode Transaksi.
                </p>
            </div>

            <div class="flex items-center shrink-0">
                <a href="{{ \App\Filament\Pages\TambahInvoice::getUrl() }}"
                   wire:navigate
                   class="inline-flex items-center gap-2 rounded-lg bg-blue-600 hover:bg-blue-700 active:bg-blue-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all duration-150">
                    <x-filament::icon icon="heroicon-m-plus" class="w-4 h-4" />
                    <span>Tambah Invoice</span>
                </a>
            </div>
        </div>

        <div class="w-full" wire:ignore>
            <table id="table-kelola-invoice" class="display w-full text-left align-middle">
                <thead>
                    <tr>
                        <th class="w-16">No</th>
                        <th>No. Invoice</th>
                        <th>Tanggal Invoice</th>
                        <th>Tujuan / Bill To</th>
                        <th>Periode Transaksi</th>
                        <th class="text-center min-w-[300px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($this->invoices as $index => $inv)
                        <tr>
                            <td class="font-mono text-xs font-bold text-slate-700 dark:text-slate-300">
                                {{ str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="font-mono text-xs font-semibold text-slate-900 dark:text-white">
                                {{ $inv->no_invoice }}
                            </td>
                            <td class="tabular-nums text-slate-700 dark:text-slate-300">
                                {{ $inv->tanggal_invoice ? $inv->tanggal_invoice->format('d/m/Y') : '-' }}
                            </td>
                            <td>
                                <div class="font-semibold text-slate-900 dark:text-white">{{ $inv->tujuan }}</div>
                                @if ($inv->nama_tempat)
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $inv->nama_tempat }}</div>
                                @endif
                            </td>
                            <td class="tabular-nums text-slate-700 dark:text-slate-300 font-medium">
                                {{ $inv->periode_transaksi }}
                            </td>
                            <td class="text-center">
                                <div class="inline-flex items-center justify-center gap-2">
                                    <a href="{{ \App\Filament\Pages\DetailInvoice::getUrl(['record' => $inv->id]) }}"
                                       wire:navigate
                                       class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-gray-600 px-3 py-1.5 text-xs font-semibold transition-colors">
                                        <x-filament::icon icon="heroicon-m-eye" class="w-3.5 h-3.5" />
                                        <span>Detail</span>
                                    </a>

                                    <a href="{{ route('kelola-invoice.pdf', ['record' => $inv->id]) }}"
                                       target="_blank"
                                       class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 px-3 py-1.5 text-xs font-semibold transition-colors">
                                        <x-filament::icon icon="heroicon-m-printer" class="w-3.5 h-3.5" />
                                        <span>Cetak PDF</span>
                                    </a>

                                    <a href="{{ route('kelola-invoice.excel', ['record' => $inv->id]) }}"
                                       class="inline-flex items-center gap-1.5 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-700 border border-teal-200 px-3 py-1.5 text-xs font-semibold transition-colors">
                                        <x-filament::icon icon="heroicon-m-table-cells" class="w-3.5 h-3.5" />
                                        <span>Cetak Excel</span>
                                    </a>

                                    <a href="{{ \App\Filament\Pages\EditInvoice::getUrl(['record' => $inv->id]) }}"
                                       wire:navigate
                                       class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 px-3 py-1.5 text-xs font-semibold transition-colors">
                                        <x-filament::icon icon="heroicon-m-pencil-square" class="w-3.5 h-3.5" />
                                        <span>Edit</span>
                                    </a>

                                    <button type="button"
                                            onclick="if(confirm('Yakin ingin menghapus Invoice {{ addslashes($inv->no_invoice) }}?')) { @this.call('hapusInvoice', {{ $inv->id }}); }"
                                            class="inline-flex items-center gap-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 px-3 py-1.5 text-xs font-semibold transition-colors">
                                        <x-filament::icon icon="heroicon-m-trash" class="w-3.5 h-3.5" />
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        (function () {
            function initTable() {
                if (typeof jQuery === 'undefined' || typeof DataTable === 'undefined') {
                    setTimeout(initTable, 100);
                    return;
                }
                const el = document.getElementById('table-kelola-invoice');
                if (!el) return;
                if (DataTable.isDataTable(el)) {
                    new DataTable(el).destroy();
                }
                new DataTable(el, {
                    pageLength: 10,
                    autoWidth: false,
                    order: [[0, 'asc']],
                    columnDefs: [
                        { orderable: false, targets: [5] }
                    ],
                    language: {
                        search: 'Cari Invoice:',
                        lengthMenu: 'Tampilkan _MENU_ data',
                        info: 'Menampilkan _START_ - _END_ dari _TOTAL_ invoice',
                        emptyTable: 'Belum ada data invoice',
                        paginate: {
                            first: 'Awal',
                            last: 'Akhir',
                            next: 'Berikutnya',
                            previous: 'Sebelumnya'
                        }
                    }
                });
            }
            document.addEventListener('DOMContentLoaded', initTable);
            document.addEventListener('livewire:navigated', initTable);
            initTable();
        })();
    </script>
</x-filament-panels::page>
