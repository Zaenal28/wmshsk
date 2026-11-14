<x-filament-panels::page>
    @include('filament.components.datatables-assets')

    <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden antialiased">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1">
                <h3 class="text-base font-bold tracking-tight text-gray-900 dark:text-white">
                    Data Permintaan Barang Cabang
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Kelola dan cetak surat permintaan barang dari berbagai cabang klinik ke Gudang Pusat.
                </p>
            </div>

            <div class="flex items-center shrink-0">
                <a href="{{ \App\Filament\Pages\TambahPermintaan::getUrl() }}"
                   wire:navigate
                   class="inline-flex items-center gap-2 rounded-lg bg-blue-600 hover:bg-blue-700 active:bg-blue-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all duration-150">
                    <x-filament::icon icon="heroicon-m-plus" class="w-4 h-4" />
                    <span>Tambah Permintaan (Manual)</span>
                </a>
            </div>
        </div>

        <div class="w-full" wire:ignore>
            <table id="table-permintaan-barang" class="display w-full text-left align-middle">
                <thead>
                    <tr>
                        <th class="w-16">No</th>
                        <th>No Surat</th>
                        <th>Tanggal Permintaan</th>
                        <th>Cabang</th>
                        <th class="text-right">Jumlah Barang</th>
                        <th>PIC</th>
                        <th class="text-center min-w-[260px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($this->permintaanList as $index => $row)
                        <tr>
                            <td class="font-mono text-xs font-bold text-slate-700 dark:text-slate-300">
                                {{ str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="font-mono text-xs font-semibold text-slate-800 dark:text-slate-200">
                                {{ $row->no_surat }}
                            </td>
                            <td class="tabular-nums text-slate-700 dark:text-slate-300">
                                {{ $row->tanggal_permintaan ? $row->tanggal_permintaan->format('d/m/Y') : '-' }}
                            </td>
                            <td class="font-semibold text-slate-800 dark:text-slate-200">
                                {{ $row->cabang }}
                            </td>
                            <td class="text-right font-semibold tabular-nums text-slate-900 dark:text-white">
                                {{ number_format($row->jumlah_barang, 0, ',', '.') }}
                            </td>
                            <td class="text-slate-700 dark:text-slate-300 font-medium">
                                {{ $row->pic }}
                            </td>
                            <td class="text-center">
                                <div class="inline-flex items-center justify-center gap-2">
                                    <a href="{{ route('permintaan-barang.pdf', ['record' => $row->id]) }}"
                                       target="_blank"
                                       class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 px-3 py-1.5 text-xs font-semibold transition-colors">
                                        <x-filament::icon icon="heroicon-m-printer" class="w-3.5 h-3.5" />
                                        <span>Cetak PDF</span>
                                    </a>

                                    <a href="{{ \App\Filament\Pages\EditPermintaan::getUrl(['record' => $row->id]) }}"
                                       wire:navigate
                                       class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 px-3 py-1.5 text-xs font-semibold transition-colors">
                                        <x-filament::icon icon="heroicon-m-pencil-square" class="w-3.5 h-3.5" />
                                        <span>Edit</span>
                                    </a>

                                    <button type="button"
                                            onclick="if(confirm('Yakin ingin menghapus Surat Permintaan {{ addslashes($row->no_surat) }}?')) { @this.call('hapusPermintaan', {{ $row->id }}); }"
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
                const el = document.getElementById('table-permintaan-barang');
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
                        search: 'Cari Permintaan:',
                        lengthMenu: 'Tampilkan _MENU_ data',
                        info: 'Menampilkan _START_ - _END_ dari _TOTAL_ permintaan',
                        emptyTable: 'Belum ada data permintaan barang cabang',
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
