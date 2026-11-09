<x-filament-panels::page>
    @include('filament.components.datatables-assets')

    <div class="space-y-6 antialiased">
        {{-- BAGIAN 1: FORM PROSES MERACIK BARANG --}}
        <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 bg-slate-50/60 dark:bg-gray-800/40 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">
                        Form Proses Peracikan Barang (Belum Diracik + Bahan Racik &rarr; Sudah Diracik)
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Pilih barang berstatus <strong>Belum Diracik</strong>, tambahkan <strong>Bahan Racik</strong>, lalu simpan untuk mengubahnya menjadi stok <strong>Sudah Diracik</strong> dengan nama yang sama.
                    </p>
                </div>

                <a href="{{ \App\Filament\Pages\KelolaBarang::getUrl() }}" wire:navigate
                   class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 px-3.5 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200">
                    <x-filament::icon icon="heroicon-m-cube" class="w-4 h-4 text-blue-600" />
                    <span>Lihat Tabel Barang</span>
                </a>
            </div>

            <div class="p-6 space-y-6">
                {{-- Row 1: Informasi Barang Dasar (Belum Diracik) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                            Kode Racikan
                        </label>
                        <input type="text" wire:model="kode_racik"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3.5 py-2 text-sm font-mono font-bold text-gray-900 dark:text-white outline-none" />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                            Tanggal Meracik <span class="text-rose-600">*</span>
                        </label>
                        <input type="date" wire:model="tanggal_racik"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2 text-sm text-gray-900 dark:text-white outline-none" />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                            PIC Peracik <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" wire:model="pic" placeholder="Contoh: RAHMA"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2 text-sm text-gray-900 dark:text-white outline-none" />
                    </div>

                    <div class="lg:col-span-1">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                            Pilih Barang (Belum Diracik) <span class="text-rose-600">*</span>
                        </label>
                        <select wire:model.live="barang_asal_id"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2 text-sm text-gray-900 dark:text-white outline-none">
                            <option value="">-- Pilih Barang Belum Diracik --</option>
                            @foreach ($this->barangBelumDiracik as $bAsal)
                                <option value="{{ $bAsal->id }}">
                                    [{{ $bAsal->no }}] {{ $bAsal->nama_barang }} (Stok Belum Diracik: {{ $bAsal->jumlah }} {{ $bAsal->satuan }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                            Jumlah yang Diracik <span class="text-rose-600">*</span>
                        </label>
                        <input type="number" min="1" wire:model="jumlah_racik" placeholder="Masukkan jumlah..."
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2 text-sm font-bold text-gray-900 dark:text-white outline-none" />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                            Expired Date Hasil Racikan
                        </label>
                        <input type="date" wire:model="tanggal_expired_hasil"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2 text-sm text-gray-900 dark:text-white outline-none" />
                    </div>
                </div>

                {{-- Row 2: Pilih Bahan Racik Campuran --}}
                <div class="p-5 rounded-xl bg-purple-50/40 dark:bg-gray-800/50 border border-purple-200/70 dark:border-gray-700 space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-bold text-purple-950 dark:text-purple-300">
                            Campuran Bahan Racik (Diambil dari Barang Jenis "Bahan Racik")
                        </h4>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-12 items-end gap-4">
                        <div class="md:col-span-7">
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                Pilih Bahan Racik
                            </label>
                            <select wire:model="selected_bahan_id"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2 text-sm text-gray-900 dark:text-white outline-none">
                                <option value="">-- Pilih Bahan Racik --</option>
                                @foreach ($this->daftarBahanRacik as $bhn)
                                    <option value="{{ $bhn->id }}">
                                        [{{ $bhn->no }}] {{ $bhn->nama_barang }} (Sisa Stok: {{ $bhn->jumlah }} {{ $bhn->satuan }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-3">
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                Jumlah Bahan Dipakai
                            </label>
                            <input type="number" min="1" wire:model="input_jumlah_bahan" placeholder="Jumlah..."
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2 text-sm font-semibold text-gray-900 dark:text-white outline-none" />
                        </div>

                        <div class="md:col-span-2">
                            <button type="button"
                                    wire:click="tambahBahanCampuran"
                                    class="w-full inline-flex items-center justify-center gap-1.5 rounded-lg bg-purple-600 hover:bg-purple-700 px-4 py-2 text-sm font-bold text-white shadow-sm transition">
                                <x-filament::icon icon="heroicon-m-plus" class="w-4 h-4" />
                                <span>Tambah Bahan</span>
                            </button>
                        </div>
                    </div>

                    {{-- Tabel Bahan Racik yang Dipakai --}}
                    <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 text-xs font-bold uppercase text-gray-700 dark:text-gray-300">
                                    <th class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700 w-14">No</th>
                                    <th class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700">Kode Bahan</th>
                                    <th class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700">Nama Bahan Racik</th>
                                    <th class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700 text-right">Jumlah Dipakai</th>
                                    <th class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700">Satuan</th>
                                    <th class="py-2.5 px-4 text-center w-24">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($bahanItems as $idx => $bRow)
                                    <tr>
                                        <td class="py-2 px-4 border-r border-gray-200 dark:border-gray-700 font-mono text-xs font-bold">{{ $idx + 1 }}</td>
                                        <td class="py-2 px-4 border-r border-gray-200 dark:border-gray-700 font-mono text-xs">{{ $bRow['no_bahan'] }}</td>
                                        <td class="py-2 px-4 border-r border-gray-200 dark:border-gray-700 font-semibold">{{ $bRow['nama_bahan'] }}</td>
                                        <td class="py-2 px-4 border-r border-gray-200 dark:border-gray-700 text-right font-bold tabular-nums">{{ number_format((int) $bRow['jumlah_pakai'], 0, ',', '.') }}</td>
                                        <td class="py-2 px-4 border-r border-gray-200 dark:border-gray-700">{{ $bRow['satuan'] }}</td>
                                        <td class="py-2 px-4 text-center">
                                            <button type="button" wire:click="hapusBahanCampuran({{ $idx }})"
                                                    class="rounded bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 px-2.5 py-1 text-xs font-semibold">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-5 text-center text-xs text-gray-400">
                                            Belum ada bahan racik campuran yang ditambahkan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Tombol Eksekusi Racikan --}}
                <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-200 dark:border-gray-800">
                    <button type="button"
                            wire:click="prosesSimpanRacikan"
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-600 hover:bg-blue-700 px-6 py-2.5 text-sm font-bold text-white shadow-sm transition">
                        <x-filament::icon icon="heroicon-m-check-badge" class="w-5 h-5" />
                        <span>Selesaikan Racikan & Tambah ke Stok Sudah Diracik</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- BAGIAN 2: TABEL RIWAYAT PERACIKAN BARANG --}}
        <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Riwayat Proses Peracikan Barang</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Catatan konversi barang Belum Diracik beserta rincian Bahan Racik yang digunakan.</p>
            </div>

            <div class="w-full" wire:ignore>
                <table id="table-kelola-racikan" class="display w-full text-left align-middle">
                    <thead>
                        <tr>
                            <th class="w-16">No</th>
                            <th>Kode Racik</th>
                            <th>Tanggal</th>
                            <th>Nama Barang (Hasil Racikan)</th>
                            <th class="text-right">Jumlah Jadi</th>
                            <th>Bahan Racik yang Dipakai</th>
                            <th>PIC</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($this->riwayatRacikans as $idx => $rck)
                            <tr>
                                <td class="font-mono text-xs font-bold text-slate-700 dark:text-slate-300">
                                    {{ str_pad((string) ($idx + 1), 3, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="font-mono text-xs font-semibold text-purple-700 dark:text-purple-400">
                                    {{ $rck->kode_racik }}
                                </td>
                                <td class="tabular-nums text-slate-700 dark:text-slate-300">
                                    {{ $rck->tanggal_racik ? $rck->tanggal_racik->format('d/m/Y') : '-' }}
                                </td>
                                <td class="font-bold text-slate-900 dark:text-white">
                                    {{ $rck->nama_barang }}
                                    <span class="ml-1.5 inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-blue-100 text-blue-800">
                                        Sudah Diracik
                                    </span>
                                </td>
                                <td class="text-right font-bold tabular-nums text-emerald-700 dark:text-emerald-400">
                                    +{{ number_format($rck->jumlah_racik, 0, ',', '.') }}
                                </td>
                                <td>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach ($rck->bahans as $b)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded bg-purple-50 text-purple-800 border border-purple-200 text-xs font-medium">
                                                {{ $b->nama_bahan }} ({{ $b->jumlah_pakai }} {{ $b->satuan }})
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="font-medium text-slate-700 dark:text-slate-300">
                                    {{ $rck->pic ?: '-' }}
                                </td>
                                <td class="text-center">
                                    <button type="button"
                                            onclick="if(confirm('Batalkan racikan {{ addslashes($rck->kode_racik) }} dan kembalikan stok asal?')) { @this.call('hapusRiwayatRacikan', {{ $rck->id }}); }"
                                            class="inline-flex items-center gap-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 px-3 py-1.5 text-xs font-semibold">
                                        <x-filament::icon icon="heroicon-m-trash" class="w-3.5 h-3.5" />
                                        <span>Batal / Hapus</span>
                                    </button>
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
                const el = document.getElementById('table-kelola-racikan');
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
                        search: 'Cari Racikan:',
                        lengthMenu: 'Tampilkan _MENU_ data',
                        info: 'Menampilkan _START_ - _END_ dari _TOTAL_ riwayat racikan',
                        emptyTable: 'Belum ada riwayat proses racikan',
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
