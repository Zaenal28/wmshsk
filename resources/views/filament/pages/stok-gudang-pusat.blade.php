<x-filament-panels::page>
    <div class="space-y-6 antialiased">
        
        <!-- Bar Kontrol Periode & Aksi Cepat -->
        <div class="rounded-2xl bg-white dark:bg-gray-900 p-5 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                
                <!-- Judul & Periode Active -->
                <div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                            <x-filament::icon icon="heroicon-o-table-cells" class="w-6 h-6" />
                        </span>
                        <div>
                            <h2 class="text-lg font-bold tracking-tight text-gray-900 dark:text-white">
                                STOK GUDANG PUSAT {{ strtoupper($this->periode_label) }}
                            </h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Monitoring buffer stock, rekapitulasi mutasi pengeluaran cabang & rekomendasi reorder ke pabrik.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi Utama -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <button type="button"
                            wire:click="sinkronkanDariWms"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 px-3.5 py-2.5 text-xs font-bold text-white shadow-sm transition-all duration-150 cursor-pointer">
                        <x-filament::icon icon="heroicon-m-arrow-path" class="w-4 h-4" wire:loading.class="animate-spin" />
                        <span>Sinkronkan dari Transaksi WMS</span>
                    </button>

                    <button type="button"
                            wire:click="bukaModalImport"
                            class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 px-3.5 py-2.5 text-xs font-bold text-white shadow-sm transition-all duration-150 cursor-pointer">
                        <x-filament::icon icon="heroicon-m-arrow-up-tray" class="w-4 h-4" />
                        <span>Import Excel / CSV</span>
                    </button>

                    <button type="button"
                            wire:click="exportExcel"
                            class="inline-flex items-center gap-2 rounded-xl bg-slate-800 hover:bg-slate-900 active:bg-black px-3.5 py-2.5 text-xs font-bold text-white shadow-sm transition-all duration-150 cursor-pointer">
                        <x-filament::icon icon="heroicon-m-arrow-down-tray" class="w-4 h-4" />
                        <span>Download Excel (.xls)</span>
                    </button>
                </div>
            </div>

            <!-- Filter Periode & Pencarian -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Bulan</label>
                    <select wire:model.live="selectedMonth"
                            class="w-full text-xs font-semibold rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-2 px-3 shadow-xs focus:ring-purple-500 focus:border-purple-500">
                        @foreach ([1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'] as $num => $nama)
                            <option value="{{ $num }}">{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Tahun</label>
                    <select wire:model.live="selectedYear"
                            class="w-full text-xs font-semibold rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-2 px-3 shadow-xs focus:ring-purple-500 focus:border-purple-500">
                        @for ($y = 2024; $y <= 2030; $y++)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Kategori</label>
                    <select wire:model.live="filterKategori"
                            class="w-full text-xs font-semibold rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-2 px-3 shadow-xs focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Semua Kategori</option>
                        @foreach ($this->kategori_list as $kat)
                            <option value="{{ $kat }}">{{ $kat }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Status Pesan</label>
                    <select wire:model.live="filterStatus"
                            class="w-full text-xs font-semibold rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-2 px-3 shadow-xs focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Semua Status</option>
                        <option value="Pesan">Wajib Pesan (Defisit)</option>
                        <option value="Tidak">Stok Aman (Tidak)</option>
                        <option value="Discontinue">Discontinue</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Cari Produk</label>
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Ketik nama / no barang..."
                           class="w-full text-xs rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-2 px-3 shadow-xs focus:ring-purple-500 focus:border-purple-500" />
                </div>
            </div>
        </div>

        <!-- KPI Ringkasan Cepat -->
        @php
            $allRows = $this->perencanaan_stoks_grouped->flatten(1);
            $totalProduk = $allRows->count();
            $totalPesan = $allRows->filter(fn ($r) => strtolower($r->status_pesan) === 'pesan')->count();
            $totalMasuk = $allRows->sum(fn ($r) => (int)$r->masuk_pabrik + (int)$r->masuk_cabang);
            $totalKeluar = $allRows->sum(fn ($r) => (int)$r->total_keluar);
        @endphp
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="rounded-2xl bg-white dark:bg-gray-900 p-4 border border-gray-200/80 dark:border-gray-800 shadow-xs flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-bold text-gray-500 uppercase">Total Produk Terdata</div>
                    <div class="text-xl font-black text-gray-900 dark:text-white mt-1">{{ number_format($totalProduk) }}</div>
                </div>
                <div class="w-9 h-9 rounded-xl bg-purple-50 dark:bg-purple-900/40 text-purple-600 flex items-center justify-center font-bold">
                    📦
                </div>
            </div>

            <div class="rounded-2xl bg-amber-50/70 dark:bg-amber-950/30 p-4 border border-amber-200 dark:border-amber-800/50 shadow-xs flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-bold text-amber-700 dark:text-amber-400 uppercase">Perlu Dipesan (Defisit)</div>
                    <div class="text-xl font-black text-amber-900 dark:text-amber-300 mt-1">{{ number_format($totalPesan) }} Item</div>
                </div>
                <div class="w-9 h-9 rounded-xl bg-amber-200 dark:bg-amber-900/60 text-amber-800 flex items-center justify-center font-bold">
                    ⚠️
                </div>
            </div>

            <div class="rounded-2xl bg-emerald-50/70 dark:bg-emerald-950/30 p-4 border border-emerald-200 dark:border-emerald-800/50 shadow-xs flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-bold text-emerald-700 dark:text-emerald-400 uppercase">Total Masuk Periode Ini</div>
                    <div class="text-xl font-black text-emerald-900 dark:text-emerald-300 mt-1">{{ number_format($totalMasuk) }} Pcs</div>
                </div>
                <div class="w-9 h-9 rounded-xl bg-emerald-200 dark:bg-emerald-900/60 text-emerald-800 flex items-center justify-center font-bold">
                    📥
                </div>
            </div>

            <div class="rounded-2xl bg-rose-50/70 dark:bg-rose-950/30 p-4 border border-rose-200 dark:border-rose-800/50 shadow-xs flex items-center justify-between">
                <div>
                    <div class="text-[11px] font-bold text-rose-700 dark:text-rose-400 uppercase">Total Keluar Periode Ini</div>
                    <div class="text-xl font-black text-rose-900 dark:text-rose-300 mt-1">{{ number_format($totalKeluar) }} Pcs</div>
                </div>
                <div class="w-9 h-9 rounded-xl bg-rose-200 dark:bg-rose-900/60 text-rose-800 flex items-center justify-center font-bold">
                    📤
                </div>
            </div>
        </div>

        <!-- Tabel Spreadsheet STOK GUDANG PUSAT (Persis Sesuai Gambar) -->
        <div class="rounded-2xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden">
            <div class="overflow-x-auto w-full">
                <table class="min-w-[1700px] w-full text-xs border-collapse">
                    <thead>
                        <!-- Baris 1: Header Utama Berkelompok -->
                        <tr class="text-center font-bold text-slate-800 dark:text-slate-100 border-b border-slate-300 dark:border-slate-700">
                            <th rowspan="2" class="p-2.5 bg-pink-100 dark:bg-pink-950/60 border-r border-slate-300 dark:border-slate-700 w-12">no.</th>
                            <th rowspan="2" class="p-2.5 bg-pink-100 dark:bg-pink-950/60 border-r border-slate-300 dark:border-slate-700 text-left min-w-[200px]">Nama Produk</th>
                            <th rowspan="2" class="p-2 bg-slate-200 dark:bg-slate-800 border-r border-slate-300 dark:border-slate-700 w-20">Rata-rata<br>3 bulan</th>
                            <th rowspan="2" class="p-2 bg-slate-200 dark:bg-slate-800 border-r border-slate-300 dark:border-slate-700 w-20">Rata rata<br>2 bulan</th>
                            <th rowspan="2" class="p-2 bg-slate-200 dark:bg-slate-800 border-r border-slate-300 dark:border-slate-700 w-20">Rata-rata<br>1 bulan</th>
                            <th rowspan="2" class="p-2 bg-slate-100 dark:bg-slate-800/70 border-r border-slate-300 dark:border-slate-700 w-16">Stok<br>awal</th>
                            
                            <!-- Subheader Masuk -->
                            <th colspan="2" class="p-2 bg-slate-200 dark:bg-slate-800 border-r border-slate-300 dark:border-slate-700">Barang Masuk</th>
                            
                            <!-- Subheader Pengeluaran -->
                            <th colspan="6" class="p-2 bg-slate-200 dark:bg-slate-800 border-r border-slate-300 dark:border-slate-700">PENGELUARAN</th>
                            
                            <th rowspan="2" class="p-2 bg-slate-200 dark:bg-slate-800 border-r border-slate-300 dark:border-slate-700 w-16">Total<br>keluar</th>
                            <th rowspan="2" class="p-2 bg-pink-100 dark:bg-pink-950/60 border-r border-slate-300 dark:border-slate-700 w-16">retur<br>pabrik</th>
                            <th rowspan="2" class="p-2 bg-slate-100 dark:bg-slate-800/80 border-r border-slate-300 dark:border-slate-700 w-16">Stok<br>akhir</th>
                            
                            <!-- Selisih -->
                            <th rowspan="2" class="p-2 bg-slate-200 dark:bg-slate-800 border-r border-slate-300 dark:border-slate-700 w-24">Kekurangan (-)<br>Kelebihan (+)<br><span class="text-[10px] text-purple-700 dark:text-purple-300">3 bulan</span></th>
                            <th rowspan="2" class="p-2 bg-slate-200 dark:bg-slate-800 border-r border-slate-300 dark:border-slate-700 w-24">Kekurangan (-)<br>Kelebihan (+)<br><span class="text-[10px] text-purple-700 dark:text-purple-300">2 bulan</span></th>
                            
                            <th rowspan="2" class="p-2 bg-pink-100 dark:bg-pink-950/60 border-r border-slate-300 dark:border-slate-700 w-24">Pesan</th>
                            <th rowspan="2" class="p-2 bg-pink-100 dark:bg-pink-950/60 border-r border-slate-300 dark:border-slate-700 w-24">TANGGAL<br>PESAN</th>
                            <th rowspan="2" class="p-2 bg-pink-100 dark:bg-pink-950/60 border-r border-slate-300 dark:border-slate-700 w-20">STOK<br>PABRIK</th>
                            <th rowspan="2" class="p-2 bg-pink-100 dark:bg-pink-950/60 border-r border-slate-300 dark:border-slate-700 w-20">expired 1</th>
                            <th rowspan="2" class="p-2 bg-pink-100 dark:bg-pink-950/60 border-r border-slate-300 dark:border-slate-700 w-20">expired 2</th>
                            <th rowspan="2" class="p-2 bg-slate-200 dark:bg-slate-800 w-16">Aksi</th>
                        </tr>

                        <!-- Baris 2: Sub-kolom Detail -->
                        <tr class="text-center font-bold text-slate-700 dark:text-slate-300 border-b border-slate-300 dark:border-slate-700 text-[11px]">
                            <!-- Masuk -->
                            <th class="p-1.5 bg-slate-100 dark:bg-slate-800/80 border-r border-slate-300 dark:border-slate-700 w-14">Pabrik</th>
                            <th class="p-1.5 bg-slate-100 dark:bg-slate-800/80 border-r border-slate-300 dark:border-slate-700 w-14">Cabang</th>

                            <!-- Pengeluaran Color Coded -->
                            <th class="p-1.5 bg-blue-100 dark:bg-blue-950/50 text-blue-900 dark:text-blue-200 border-r border-slate-300 dark:border-slate-700 w-12">AP</th>
                            <th class="p-1.5 bg-purple-100 dark:bg-purple-950/50 text-purple-900 dark:text-purple-200 border-r border-slate-300 dark:border-slate-700 w-12">RF</th>
                            <th class="p-1.5 bg-rose-100 dark:bg-rose-950/50 text-rose-900 dark:text-rose-200 border-r border-slate-300 dark:border-slate-700 w-12">MP</th>
                            <th class="p-1.5 bg-amber-100 dark:bg-amber-950/50 text-amber-900 dark:text-amber-200 border-r border-slate-300 dark:border-slate-700 w-14">PWK</th>
                            <th class="p-1.5 bg-emerald-100 dark:bg-emerald-950/50 text-emerald-900 dark:text-emerald-200 border-r border-slate-300 dark:border-slate-700 w-12">BDG</th>
                            <th class="p-1.5 bg-yellow-100 dark:bg-yellow-950/50 text-yellow-900 dark:text-yellow-200 border-r border-slate-300 dark:border-slate-700 w-14">RACIK</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse ($this->perencanaan_stoks_grouped as $kategori => $items)
                            <!-- Baris Header Kategori (Pembersih, Sabun, Sunblock, Pelembab, Serum, dll) -->
                            <tr class="bg-purple-50/80 dark:bg-purple-950/40 font-bold border-y-2 border-purple-200 dark:border-purple-800">
                                <td colspan="23" class="py-2 px-4 text-purple-900 dark:text-purple-200 text-center tracking-wide uppercase text-xs">
                                    ✦ {{ $kategori }} ({{ $items->count() }} Produk) ✦
                                </td>
                            </tr>

                            @foreach ($items as $item)
                                @php
                                    $pesanLower = strtolower($item->status_pesan);
                                    $badge = $item->badge_status_pesan;
                                    $isDefisit3 = $item->selisih_3_bulan < 0;
                                    $isDefisit2 = $item->selisih_2_bulan < 0;
                                @endphp
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition border-b border-slate-200 dark:border-slate-800 text-center text-slate-800 dark:text-slate-200">
                                    <td class="p-2 border-r border-slate-200 dark:border-slate-800 font-mono text-[11px] font-bold text-slate-500">
                                        {{ $item->no_urut ?: $loop->iteration }}
                                    </td>
                                    <td class="p-2 border-r border-slate-200 dark:border-slate-800 text-left font-bold text-slate-900 dark:text-white">
                                        {{ $item->nama_barang }}
                                    </td>
                                    <td class="p-2 border-r border-slate-200 dark:border-slate-800 font-semibold tabular-nums">
                                        {{ $item->rata_3_bulan ?: '-' }}
                                    </td>
                                    <td class="p-2 border-r border-slate-200 dark:border-slate-800 font-semibold tabular-nums">
                                        {{ $item->rata_2_bulan ?: '-' }}
                                    </td>
                                    <td class="p-2 border-r border-slate-200 dark:border-slate-800 font-semibold tabular-nums">
                                        {{ $item->rata_1_bulan ?: '-' }}
                                    </td>
                                    <td class="p-2 border-r border-slate-200 dark:border-slate-800 font-bold tabular-nums bg-slate-50/50 dark:bg-slate-800/40">
                                        {{ $item->stok_awal ?: 0 }}
                                    </td>

                                    <!-- Masuk -->
                                    <td class="p-2 border-r border-slate-200 dark:border-slate-800 tabular-nums {{ $item->masuk_pabrik > 0 ? 'font-bold text-emerald-600' : 'text-slate-400' }}">
                                        {{ $item->masuk_pabrik ?: '-' }}
                                    </td>
                                    <td class="p-2 border-r border-slate-200 dark:border-slate-800 tabular-nums {{ $item->masuk_cabang > 0 ? 'font-bold text-emerald-600' : 'text-slate-400' }}">
                                        {{ $item->masuk_cabang ?: '-' }}
                                    </td>

                                    <!-- Pengeluaran -->
                                    <td class="p-2 border-r border-slate-200 dark:border-slate-800 tabular-nums bg-blue-50/30 dark:bg-blue-950/20 {{ $item->keluar_ap > 0 ? 'font-bold text-blue-700 dark:text-blue-300' : 'text-slate-400' }}">
                                        {{ $item->keluar_ap ?: '-' }}
                                    </td>
                                    <td class="p-2 border-r border-slate-200 dark:border-slate-800 tabular-nums bg-purple-50/30 dark:bg-purple-950/20 {{ $item->keluar_rf > 0 ? 'font-bold text-purple-700 dark:text-purple-300' : 'text-slate-400' }}">
                                        {{ $item->keluar_rf ?: '-' }}
                                    </td>
                                    <td class="p-2 border-r border-slate-200 dark:border-slate-800 tabular-nums bg-rose-50/30 dark:bg-rose-950/20 {{ $item->keluar_mp > 0 ? 'font-bold text-rose-700 dark:text-rose-300' : 'text-slate-400' }}">
                                        {{ $item->keluar_mp ?: '-' }}
                                    </td>
                                    <td class="p-2 border-r border-slate-200 dark:border-slate-800 tabular-nums bg-amber-50/30 dark:bg-amber-950/20 {{ $item->keluar_pwk > 0 ? 'font-bold text-amber-700 dark:text-amber-300' : 'text-slate-400' }}">
                                        {{ $item->keluar_pwk ?: '-' }}
                                    </td>
                                    <td class="p-2 border-r border-slate-200 dark:border-slate-800 tabular-nums bg-emerald-50/30 dark:bg-emerald-950/20 {{ $item->keluar_bdg > 0 ? 'font-bold text-emerald-700 dark:text-emerald-300' : 'text-slate-400' }}">
                                        {{ $item->keluar_bdg ?: '-' }}
                                    </td>
                                    <td class="p-2 border-r border-slate-200 dark:border-slate-800 tabular-nums bg-yellow-50/30 dark:bg-yellow-950/20 {{ $item->keluar_racik > 0 ? 'font-bold text-yellow-700 dark:text-yellow-300' : 'text-slate-400' }}">
                                        {{ $item->keluar_racik ?: '-' }}
                                    </td>

                                    <!-- Total Keluar -->
                                    <td class="p-2 border-r border-slate-200 dark:border-slate-800 font-bold tabular-nums text-slate-900 dark:text-white bg-slate-50/60 dark:bg-slate-800/40">
                                        {{ $item->total_keluar ?: 0 }}
                                    </td>

                                    <!-- Retur Pabrik -->
                                    <td class="p-2 border-r border-slate-200 dark:border-slate-800 tabular-nums {{ $item->retur_pabrik > 0 ? 'font-bold text-rose-600 bg-rose-50/40' : 'text-slate-400' }}">
                                        {{ $item->retur_pabrik ?: '-' }}
                                    </td>

                                    <!-- Stok Akhir -->
                                    <td class="p-2 border-r border-slate-200 dark:border-slate-800 font-black tabular-nums text-slate-900 dark:text-white bg-slate-100/70 dark:bg-slate-800/70">
                                        {{ $item->stok_akhir ?: 0 }}
                                    </td>

                                    <!-- Selisih 3 Bulan -->
                                    <td class="p-2 border-r border-slate-200 dark:border-slate-800 font-bold tabular-nums {{ $isDefisit3 ? 'text-rose-600 dark:text-rose-400 font-black' : 'text-slate-700 dark:text-slate-300' }}">
                                        {{ $item->selisih_3_bulan }}
                                    </td>

                                    <!-- Selisih 2 Bulan -->
                                    <td class="p-2 border-r border-slate-200 dark:border-slate-800 font-bold tabular-nums {{ $isDefisit2 ? 'text-rose-600 dark:text-rose-400 font-black' : 'text-slate-700 dark:text-slate-300' }}">
                                        {{ $item->selisih_2_bulan }}
                                    </td>

                                    <!-- Status Pesan (Badge Highlight) -->
                                    <td class="p-2 border-r border-slate-200 dark:border-slate-800 font-bold">
                                        @if ($pesanLower === 'pesan')
                                            <span class="inline-block px-2.5 py-1 rounded-md text-xs font-black bg-yellow-300 text-yellow-900 shadow-xs border border-yellow-400 animate-pulse">
                                                Pesan
                                            </span>
                                        @elseif ($pesanLower === 'discontinue')
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-slate-200 text-slate-600 border border-slate-300">
                                                discontinue
                                            </span>
                                        @else
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-[11px] font-medium text-slate-600 dark:text-slate-300">
                                                Tidak
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Tanggal Pesan -->
                                    <td class="p-2 border-r border-slate-200 dark:border-slate-800 font-semibold text-slate-700 dark:text-slate-300">
                                        {{ $item->tanggal_pesan ? $item->tanggal_pesan->format('d/m') : '-' }}
                                    </td>

                                    <!-- Stok Pabrik -->
                                    <td class="p-2 border-r border-slate-200 dark:border-slate-800 font-bold tabular-nums text-purple-700 dark:text-purple-300">
                                        {{ $item->stok_pabrik ?: '-' }}
                                    </td>

                                    <!-- Expired 1 -->
                                    <td class="p-2 border-r border-slate-200 dark:border-slate-800 font-medium text-slate-700 dark:text-slate-300 text-[11px]">
                                        {{ $item->expired_1 ?: '-' }}
                                    </td>

                                    <!-- Expired 2 -->
                                    <td class="p-2 border-r border-slate-200 dark:border-slate-800 font-medium text-slate-700 dark:text-slate-300 text-[11px]">
                                        {{ $item->expired_2 ?: '-' }}
                                    </td>

                                    <!-- Tombol Edit Quick Action -->
                                    <td class="p-1.5">
                                        <button type="button"
                                                wire:click="bukaModalEdit({{ $item->id }})"
                                                class="px-2.5 py-1 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-700 font-bold text-[11px] transition cursor-pointer">
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="23" class="py-12 text-center text-slate-500">
                                    <div class="max-w-md mx-auto space-y-3">
                                        <div class="text-3xl">📭</div>
                                        <div class="font-bold text-sm text-slate-800 dark:text-slate-200">
                                            Belum ada data perencanaan stok untuk periode {{ $this->periode_label }}.
                                        </div>
                                        <p class="text-xs text-slate-500">
                                            Klik tombol <b>"Sinkronkan dari Transaksi WMS"</b> untuk menarik otomatis atau <b>"Import Excel / CSV"</b> untuk memuat data histori Anda.
                                        </p>
                                        <button type="button"
                                                wire:click="sinkronkanDariWms"
                                                class="inline-flex items-center gap-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold px-4 py-2 text-xs shadow-md transition">
                                            <x-filament::icon icon="heroicon-m-arrow-path" class="w-4 h-4" />
                                            <span>Sinkronkan Sekarang</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Quick Edit Item -->
        @if ($showEditModal)
            <div class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-800 w-full max-w-lg overflow-hidden animate-in fade-in">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Edit Perencanaan Produk</h3>
                            <p class="text-xs text-purple-600 dark:text-purple-400 font-semibold">{{ $editNamaBarang }}</p>
                        </div>
                        <button type="button" wire:click="tutupModalEdit" class="text-gray-400 hover:text-gray-600 text-base">✕</button>
                    </div>

                    <form wire:submit.prevent="simpanModalEdit" class="p-6 space-y-4 text-xs">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Status Pesan</label>
                                <select wire:model="editStatusPesan" class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-2 px-3 focus:ring-purple-500">
                                    <option value="Tidak">Tidak (Stok Aman)</option>
                                    <option value="Pesan">Pesan (Defisit Kebutuhan)</option>
                                    <option value="Discontinue">Discontinue</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Tanggal Pesan</label>
                                <input type="date" wire:model="editTanggalPesan" class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-2 px-3 focus:ring-purple-500" />
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Stok Pabrik (Estimasi Supplier)</label>
                            <input type="number" wire:model="editStokPabrik" placeholder="contoh: 148" class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-2 px-3 focus:ring-purple-500" />
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Expired 1 (Batch Lama)</label>
                                <input type="text" wire:model="editExpired1" placeholder="contoh: 5/2027" class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-2 px-3 focus:ring-purple-500" />
                            </div>
                            <div>
                                <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Expired 2 (Batch Baru Masuk)</label>
                                <input type="text" wire:model="editExpired2" placeholder="contoh: 8/2028" class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-2 px-3 focus:ring-purple-500" />
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Catatan Tambahan</label>
                            <textarea wire:model="editCatatan" rows="2" placeholder="Catatan internal pengadaan atau stok..." class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-2 px-3 focus:ring-purple-500"></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                            <button type="button" wire:click="tutupModalEdit" class="px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-700 font-bold text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold shadow-md shadow-purple-500/20">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- Modal Import Histori Excel/CSV -->
        @if ($showImportModal)
            <div class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-800 w-full max-w-md overflow-hidden animate-in fade-in">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Import File Histori Spreadsheet</h3>
                            <p class="text-xs text-gray-500">Target Periode: <span class="font-bold text-purple-600">{{ $this->periode_label }}</span></p>
                        </div>
                        <button type="button" wire:click="tutupModalImport" class="text-gray-400 hover:text-gray-600 text-base">✕</button>
                    </div>

                    <form wire:submit.prevent="prosesImportFile" class="p-6 space-y-4 text-xs">
                        <div class="p-3 bg-purple-50 dark:bg-purple-950/40 rounded-xl border border-purple-200 dark:border-purple-800 text-[11px] text-purple-900 dark:text-purple-200 space-y-1">
                            <div class="font-bold">Format Kolom File Excel / CSV:</div>
                            <p>Kolom 1: No, Kolom 2: Nama Produk, Kolom 3-5: Rata 3, 2, 1 Bulan, Kolom 6: Stok Awal, Kolom 7-8: Masuk Pabrik & Cabang, Kolom 9-14: Pengeluaran (AP, RF, MP, PWK, BDG, RACIK), Kolom 16: Retur Pabrik, Kolom 19: Status Pesan, Kolom 20: Tgl Pesan, Kolom 21: Stok Pabrik, Kolom 22-23: Expired 1 & 2.</p>
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 dark:text-gray-300 mb-2">Pilih File (.xlsx / .csv)</label>
                            <input type="file" wire:model="fileImport" accept=".xlsx,.csv,.txt" required class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 cursor-pointer" />
                            @error('fileImport') <span class="text-rose-600 text-[11px] font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                            <button type="button" wire:click="tutupModalImport" class="px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-700 font-bold text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800">
                                Batal
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-md shadow-emerald-500/20">
                                <span wire:loading.remove>Mulai Import</span>
                                <span wire:loading>Memproses...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>
</x-filament-panels::page>
