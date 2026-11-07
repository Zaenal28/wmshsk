<x-filament-panels::page>
    @include('filament.components.datatables-assets')

    <div class="mb-2 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ \App\Filament\Pages\KelolaBarang::getUrl() }}" wire:navigate
           class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400">
            <x-filament::icon icon="heroicon-m-arrow-left" class="w-4 h-4" />
            Kembali ke Tabel Kelola Barang
        </a>

        <a href="{{ \App\Filament\Pages\EditBarang::getUrl(['record' => $barang->id]) }}" wire:navigate
           class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 px-4 py-2 text-xs font-semibold text-white shadow-sm transition">
            <x-filament::icon icon="heroicon-m-pencil-square" class="w-4 h-4" />
            <span>Edit Data Barang</span>
        </a>
    </div>

    @php
        $status = $barang->status_expired_info;
        $totalKeluar = $this->riwayatKeluar->sum('jumlah');
    @endphp

    <div class="space-y-6 antialiased">
        {{-- BAGIAN 1: INFORMASI LENGKAP DATA BARANG --}}
        <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex flex-wrap items-center justify-between gap-4 bg-slate-50/60 dark:bg-gray-800/40">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Informasi Lengkap Barang</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Detail master barang, stok gudang pusat, harga, dan masa berlaku.</p>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-xs font-medium text-gray-500">Status Expired:</span>
                    <span style="background-color: {{ $status['bg'] }}; color: {{ $status['text'] }}; border: 1px solid {{ $status['border'] }};"
                          class="inline-flex items-center justify-center min-w-[84px] px-3 py-1 rounded-full text-xs font-bold tracking-wide">
                        {{ $status['label'] }}
                    </span>
                </div>
            </div>

            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="p-4 rounded-lg bg-slate-50 dark:bg-gray-800/60 border border-slate-200/70 dark:border-gray-700">
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">No / Kode Barang</div>
                    <div class="mt-1 text-base font-mono font-bold text-gray-900 dark:text-white">{{ $barang->no }}</div>
                </div>

                <div class="p-4 rounded-lg bg-slate-50 dark:bg-gray-800/60 border border-slate-200/70 dark:border-gray-700">
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Nama Barang</div>
                    <div class="mt-1 text-base font-bold text-gray-900 dark:text-white">{{ $barang->nama_barang }}</div>
                </div>

                <div class="p-4 rounded-lg bg-slate-50 dark:bg-gray-800/60 border border-slate-200/70 dark:border-gray-700">
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Supplier</div>
                    <div class="mt-1 text-base font-semibold text-gray-900 dark:text-white">{{ $barang->supplier ?: '-' }}</div>
                </div>

                <div class="p-4 rounded-lg bg-slate-50 dark:bg-gray-800/60 border border-slate-200/70 dark:border-gray-700">
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Jenis / Kategori Barang</div>
                    <div class="mt-1 text-base font-semibold text-gray-900 dark:text-white">{{ $barang->kategori ?: '-' }}</div>
                </div>

                <div class="p-4 rounded-lg bg-slate-50 dark:bg-gray-800/60 border border-slate-200/70 dark:border-gray-700">
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Alokasi Penyimpanan</div>
                    <div class="mt-1 text-base font-semibold text-gray-900 dark:text-white">{{ $barang->alokasi_penyimpanan ?: 'Gudang Pusat Lt1' }}</div>
                </div>

                <div class="p-4 rounded-lg bg-slate-50 dark:bg-gray-800/60 border border-slate-200/70 dark:border-gray-700">
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Stok Tersedia & Satuan</div>
                    <div class="mt-1 text-base font-bold text-blue-600 dark:text-blue-400 tabular-nums">
                        {{ number_format($barang->jumlah, 0, ',', '.') }} {{ $barang->satuan ?: 'Pcs' }}
                    </div>
                </div>

                <div class="p-4 rounded-lg bg-slate-50 dark:bg-gray-800/60 border border-slate-200/70 dark:border-gray-700">
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Harga Barang</div>
                    <div class="mt-1 text-base font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">
                        Rp {{ number_format((float) $barang->harga_barang, 0, ',', '.') }}
                    </div>
                </div>

                <div class="p-4 rounded-lg bg-slate-50 dark:bg-gray-800/60 border border-slate-200/70 dark:border-gray-700">
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Tanggal Masuk</div>
                    <div class="mt-1 text-base font-semibold text-gray-900 dark:text-white tabular-nums">
                        {{ $barang->tanggal_masuk ? $barang->tanggal_masuk->format('d/m/Y') : '-' }}
                    </div>
                </div>

                <div class="p-4 rounded-lg bg-slate-50 dark:bg-gray-800/60 border border-slate-200/70 dark:border-gray-700">
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Tanggal Expired</div>
                    <div class="mt-1 text-base font-semibold text-gray-900 dark:text-white tabular-nums">
                        {{ $barang->tanggal_expired ? $barang->tanggal_expired->format('d/m/Y') : '-' }}
                    </div>
                </div>

                <div class="p-4 rounded-lg bg-slate-50 dark:bg-gray-800/60 border border-slate-200/70 dark:border-gray-700">
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Masa Berlaku</div>
                    <div class="mt-1 text-base font-semibold text-gray-900 dark:text-white">
                        {{ $barang->masa_berlaku }}
                    </div>
                </div>
            </div>
        </div>

        {{-- BAGIAN 2: TABEL RIWAYAT BARANG KELUAR (TUJUAN & PIC) --}}
        <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">
                        Riwayat Pengeluaran Barang ({{ $barang->nama_barang }})
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Daftar distribusi barang keluar ke masing-masing tujuan beserta PIC yang mengeluarkan.
                    </p>
                </div>

                <div class="inline-flex items-center gap-2 rounded-lg bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 px-3.5 py-1.5 text-xs font-bold text-blue-700 dark:text-blue-300">
                    <span>Total Dikeluarkan:</span>
                    <span class="tabular-nums">{{ number_format($totalKeluar, 0, ',', '.') }} {{ $barang->satuan ?: 'Pcs' }}</span>
                </div>
            </div>

            <div class="w-full" wire:ignore>
                <table id="table-detail-barang-keluar" class="display w-full text-left align-middle">
                    <thead>
                        <tr>
                            <th class="w-16">No</th>
                            <th>No Surat Jalan</th>
                            <th>Tanggal Keluar</th>
                            <th>Tujuan</th>
                            <th>PIC (Yang Mengeluarkan)</th>
                            <th>Nama Penerima</th>
                            <th class="text-right">Jumlah Keluar</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($this->riwayatKeluar as $index => $row)
                            @php
                                $header = $row->barangKeluar;
                            @endphp
                            <tr>
                                <td class="font-mono text-xs font-bold text-slate-700 dark:text-slate-300">
                                    {{ str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="font-mono text-xs font-semibold text-slate-800 dark:text-slate-200">
                                    {{ $header?->no_surat ?? '-' }}
                                </td>
                                <td class="tabular-nums text-slate-700 dark:text-slate-300">
                                    {{ $header?->tanggal_keluar ? $header->tanggal_keluar->format('d/m/Y') : '-' }}
                                </td>
                                <td>
                                    <span class="inline-flex items-center rounded-md bg-amber-50 dark:bg-amber-900/30 px-2.5 py-1 text-xs font-bold text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-700">
                                        {{ $header?->tujuan ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="inline-flex items-center gap-1.5 font-semibold text-slate-900 dark:text-white">
                                        <x-filament::icon icon="heroicon-m-user-circle" class="w-4 h-4 text-blue-600" />
                                        {{ $header?->pic ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-slate-700 dark:text-slate-300 font-medium">
                                    {{ $header?->nama_penerima ?: '-' }}
                                </td>
                                <td class="text-right font-bold tabular-nums text-rose-600 dark:text-rose-400">
                                    {{ number_format($row->jumlah, 0, ',', '.') }} {{ $barang->satuan ?: 'Pcs' }}
                                </td>
                                <td class="text-center">
                                    @if ($header)
                                        <a href="{{ route('barang-keluar.pdf', ['record' => $header->id]) }}"
                                           target="_blank"
                                           class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 px-3 py-1.5 text-xs font-semibold transition-colors">
                                            <x-filament::icon icon="heroicon-m-printer" class="w-3.5 h-3.5" />
                                            <span>Surat Jalan</span>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        (function () {
            function initTable() {
                if (typeof jQuery === 'undefined' || typeof DataTable === 'undefined') {
                    setTimeout(initTable, 100);
                    return;
                }
                const el = document.getElementById('table-detail-barang-keluar');
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
                        search: 'Cari Tujuan / PIC:',
                        lengthMenu: 'Tampilkan _MENU_ data',
                        info: 'Menampilkan _START_ - _END_ dari _TOTAL_ riwayat keluar',
                        emptyTable: 'Barang ini belum pernah dikeluarkan (belum ada riwayat barang keluar)',
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
