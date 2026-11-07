<x-filament-panels::page>
    @include('filament.components.datatables-assets')

    <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden antialiased">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1.5">
                <h3 class="text-base font-bold tracking-tight text-gray-900 dark:text-white">
                    Daftar Master Barang & Monitoring Expired
                </h3>
                <div class="flex flex-wrap items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                    <span class="font-medium text-gray-500">Ketentuan Status:</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Safe (&ge; 2 Tahun)
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full font-semibold bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Warning (Menuju 1 Tahun)
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full font-semibold bg-rose-50 text-rose-700 ring-1 ring-inset ring-rose-600/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Danger (Beberapa Bulan Lagi)
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full font-semibold bg-red-900 text-white">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-300"></span> Expired (Melebihi Tgl Expired)
                    </span>
                </div>
            </div>

            <div class="flex items-center shrink-0">
                <a href="{{ \App\Filament\Pages\TambahBarang::getUrl() }}"
                   wire:navigate
                   class="inline-flex items-center gap-2 rounded-lg bg-blue-600 hover:bg-blue-700 active:bg-blue-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all duration-150">
                    <x-filament::icon icon="heroicon-m-plus" class="w-4 h-4" />
                    <span>Tambah Barang</span>
                </a>
            </div>
        </div>

        <div class="w-full" wire:ignore>
            <table id="table-kelola-barang" class="display w-full text-left align-middle">
                <thead>
                    <tr>
                        <th class="w-16">No</th>
                        <th>Nama Barang</th>
                        <th class="text-right">Jumlah</th>
                        <th>Tanggal Masuk</th>
                        <th>Expired</th>
                        <th>Masa Berlaku</th>
                        <th class="text-center">Status Expired</th>
                        <th class="text-center min-w-[230px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($this->barangs as $item)
                        @php
                            $status = $item->status_expired_info;
                        @endphp
                        <tr>
                            <td class="font-mono text-xs font-bold text-slate-700 dark:text-slate-300 tracking-wider">
                                {{ $item->no }}
                            </td>
                            <td class="font-semibold text-slate-900 dark:text-white tracking-tight">
                                {{ $item->nama_barang }}
                            </td>
                            <td class="text-right font-semibold tabular-nums text-slate-800 dark:text-slate-200">
                                {{ number_format($item->jumlah, 0, ',', '.') }}
                            </td>
                            <td class="tabular-nums text-slate-600 dark:text-slate-300">
                                {{ $item->tanggal_masuk ? $item->tanggal_masuk->format('j/n/y') : '-' }}
                            </td>
                            <td class="tabular-nums text-slate-600 dark:text-slate-300">
                                {{ $item->tanggal_expired ? $item->tanggal_expired->format('j/n/y') : '-' }}
                            </td>
                            <td class="text-slate-700 dark:text-slate-300 font-medium">
                                {{ $item->masa_berlaku }}
                            </td>
                            <td class="text-center">
                                <span style="background-color: {{ $status['bg'] }}; color: {{ $status['text'] }}; border: 1px solid {{ $status['border'] }};"
                                      class="inline-flex items-center justify-center min-w-[84px] px-3 py-1 rounded-full text-xs font-bold tracking-wide shadow-2xs">
                                    {{ $status['label'] }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="inline-flex items-center justify-center gap-2">
                                    <a href="{{ \App\Filament\Pages\DetailBarang::getUrl(['record' => $item->id]) }}"
                                       wire:navigate
                                       class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 px-3 py-1.5 text-xs font-semibold transition-colors">
                                        <x-filament::icon icon="heroicon-m-eye" class="w-3.5 h-3.5" />
                                        <span>Detail</span>
                                    </a>

                                    <a href="{{ \App\Filament\Pages\EditBarang::getUrl(['record' => $item->id]) }}"
                                       wire:navigate
                                       class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 px-3 py-1.5 text-xs font-semibold transition-colors">
                                        <x-filament::icon icon="heroicon-m-pencil-square" class="w-3.5 h-3.5" />
                                        <span>Edit</span>
                                    </a>

                                    <button type="button"
                                            onclick="if(confirm('Yakin ingin menghapus barang {{ addslashes($item->nama_barang) }}?')) { @this.call('hapusBarang', {{ $item->id }}); }"
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
                const el = document.getElementById('table-kelola-barang');
                if (!el) return;
                if (DataTable.isDataTable(el)) {
                    new DataTable(el).destroy();
                }
                new DataTable(el, {
                    pageLength: 10,
                    autoWidth: false,
                    order: [[0, 'asc']],
                    columnDefs: [
                        { orderable: false, targets: [7] }
                    ],
                    language: {
                        search: 'Cari Barang:',
                        lengthMenu: 'Tampilkan _MENU_ data',
                        info: 'Menampilkan _START_ - _END_ dari _TOTAL_ barang',
                        emptyTable: 'Belum ada data barang',
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
