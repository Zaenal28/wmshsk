<x-filament-panels::page>
    @include('filament.components.datatables-assets')

    <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden antialiased">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1">
                <h3 class="text-base font-bold tracking-tight text-gray-900 dark:text-white">
                    Data Transaksi Barang Keluar & Surat Jalan
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Kelola pengeluaran barang dari Gudang Pusat beserta cetak dokumen Surat Jalan (PDF).
                </p>
            </div>

            <div class="flex items-center shrink-0">
                <a href="{{ \App\Filament\Pages\TambahBarangKeluar::getUrl() }}"
                   wire:navigate
                   class="inline-flex items-center gap-2 rounded-lg bg-blue-600 hover:bg-blue-700 active:bg-blue-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all duration-150">
                    <x-filament::icon icon="heroicon-m-plus" class="w-4 h-4" />
                    <span>Tambah Barang Keluar</span>
                </a>
            </div>
        </div>

        <div class="w-full" wire:ignore>
            <table id="table-barang-keluar" class="display w-full text-left align-middle">
                <thead>
                    <tr>
                        <th class="w-16">No</th>
                        <th>No Surat</th>
                        <th>Tanggal Keluar</th>
                        <th class="text-right">Jumlah Barang</th>
                        <th>Tujuan</th>
                        <th>PIC</th>
                        <th class="text-center min-w-[260px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($this->barangKeluars as $index => $row)
                        <tr>
                            <td class="font-mono text-xs font-bold text-slate-700 dark:text-slate-300">
                                {{ str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="font-mono text-xs font-semibold text-slate-800 dark:text-slate-200">
                                {{ $row->no_surat }}
                            </td>
                            <td class="tabular-nums text-slate-700 dark:text-slate-300">
                                {{ $row->tanggal_keluar ? $row->tanggal_keluar->format('d/m/Y') : '-' }}
                            </td>
                            <td class="text-right font-semibold tabular-nums text-slate-900 dark:text-white">
                                {{ number_format($row->total_jumlah, 0, ',', '.') }}
                            </td>
                            <td class="font-semibold text-slate-800 dark:text-slate-200">
                                {{ $row->tujuan }}
                            </td>
                            <td class="text-slate-700 dark:text-slate-300 font-medium">
                                {{ $row->pic }}
                            </td>
                            <td class="text-center">
                                <div class="inline-flex items-center justify-center gap-2">
                                    <a href="{{ route('barang-keluar.pdf', ['record' => $row->id]) }}"
                                       target="_blank"
                                       class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 px-3 py-1.5 text-xs font-semibold transition-colors">
                                        <x-filament::icon icon="heroicon-m-printer" class="w-3.5 h-3.5" />
                                        <span>Cetak PDF</span>
                                    </a>

                                    <a href="{{ \App\Filament\Pages\EditBarangKeluar::getUrl(['record' => $row->id]) }}"
                                       wire:navigate
                                       class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 px-3 py-1.5 text-xs font-semibold transition-colors">
                                        <x-filament::icon icon="heroicon-m-pencil-square" class="w-3.5 h-3.5" />
                                        <span>Edit</span>
                                    </a>

                                    <button type="button"
                                            onclick="if(confirm('Yakin ingin menghapus transaksi Surat Jalan {{ addslashes($row->no_surat) }}?')) { @this.call('hapusBarangKeluar', {{ $row->id }}); }"
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
                const el = document.getElementById('table-barang-keluar');
                if (!el) return;
                if (DataTable.isDataTable(el)) {
                    new DataTable(el).destroy();
                }
                new DataTable(el, {
                    pageLength: 10,
                    autoWidth: false,
                    order: [[0, 'asc']],
                    columnDefs: [
                        { orderable: false, targets: [6] }
                    ],
                    language: {
                        search: 'Cari Transaksi:',
                        lengthMenu: 'Tampilkan _MENU_ data',
                        info: 'Menampilkan _START_ - _END_ dari _TOTAL_ transaksi',
                        emptyTable: 'Belum ada transaksi barang keluar',
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
