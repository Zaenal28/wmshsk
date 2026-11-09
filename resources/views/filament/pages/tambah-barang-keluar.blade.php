<x-filament-panels::page>
    <div class="mb-2 flex items-center justify-between">
        <a href="{{ \App\Filament\Pages\BarangKeluar::getUrl() }}" wire:navigate
           class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400">
            <x-filament::icon icon="heroicon-m-arrow-left" class="w-4 h-4" />
            Kembali ke Tabel Barang Keluar
        </a>
    </div>

    <div class="space-y-6 antialiased">
        {{-- BAGIAN 1: FORM INPUT HEADER BARANG KELUAR --}}
        <div class="relative z-30 rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-visible">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 bg-slate-50/60 dark:bg-gray-800/40 rounded-t-xl">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Form Informasi Surat Jalan / Barang Keluar</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Lengkapi informasi dokumen pengeluaran barang di bawah ini.</p>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                        Tanggal Keluar <span class="text-rose-600">*</span>
                    </label>
                    <input type="date" wire:model="tanggal_keluar"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2 text-sm text-gray-900 dark:text-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none" />
                    @error('tanggal_keluar') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                        No Surat <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" wire:model="no_surat" placeholder="Contoh: 2710 /HSKBIO/ X /2026"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2 text-sm font-mono text-gray-900 dark:text-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none" />
                    @error('no_surat') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                        PIC (Admin Gudang Pusat) <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" wire:model="pic" placeholder="Contoh: RAHMA"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2 text-sm text-gray-900 dark:text-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none" />
                    @error('pic') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div x-data="{
                        openTujuan: false,
                        filterQuery: '',
                        tujuanVal: @entangle('tujuan').live,
                        options: @js(\App\Models\BarangKeluar::getTujuanList()),
                        get filteredOptions() {
                            if (!this.filterQuery || this.filterQuery.trim() === '') {
                                return this.options;
                            }
                            const q = this.filterQuery.toLowerCase();
                            return this.options.filter(o => o.toLowerCase().includes(q));
                        },
                        get canAddNew() {
                            const text = (this.tujuanVal || '').trim();
                            if (!text) return false;
                            const q = text.toLowerCase();
                            return !this.options.some(o => o.toLowerCase() === q);
                        },
                        selectTujuan(val) {
                            this.tujuanVal = val;
                            this.filterQuery = '';
                            this.openTujuan = false;
                        },
                        addNewTujuan() {
                            const newVal = (this.tujuanVal || '').trim();
                            if (newVal && !this.options.includes(newVal)) {
                                this.options.push(newVal);
                            }
                            this.tujuanVal = newVal;
                            this.filterQuery = '';
                            this.openTujuan = false;
                        }
                    }"
                    @click.outside="openTujuan = false">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                        Tujuan <span class="text-rose-600">*</span>
                    </label>
                    <div class="relative">
                        <input type="text"
                               x-model="tujuanVal"
                               @focus="filterQuery = ''; openTujuan = true"
                               @click="filterQuery = ''; openTujuan = true"
                               @input="filterQuery = $event.target.value; openTujuan = true"
                               @keydown.escape.window="openTujuan = false"
                               placeholder="--Pilih atau ketik tujuan baru--"
                               autocomplete="off"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 pl-3.5 pr-9 py-2 text-sm text-gray-900 dark:text-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none" />

                        <button type="button"
                                @click="filterQuery = ''; openTujuan = !openTujuan"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                            <x-filament::icon icon="heroicon-m-chevron-up-down" class="w-4 h-4" />
                        </button>

                        <div x-show="openTujuan"
                             x-transition
                             style="display: none;"
                             class="absolute z-50 mt-1 w-full max-h-60 overflow-y-auto rounded-lg bg-white dark:bg-gray-800 shadow-xl border border-gray-200 dark:border-gray-700 py-1 text-sm">
                            <template x-for="opt in filteredOptions" :key="opt">
                                <button type="button"
                                        @click="selectTujuan(opt)"
                                        class="w-full text-left px-3.5 py-2 hover:bg-blue-50 dark:hover:bg-gray-700 flex items-center justify-between text-gray-900 dark:text-white font-medium transition-colors">
                                    <span x-text="opt"></span>
                                </button>
                            </template>

                            <template x-if="canAddNew">
                                <button type="button"
                                        @click="addNewTujuan()"
                                        class="w-full text-left px-3.5 py-2 bg-blue-50/70 hover:bg-blue-100 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-300 font-semibold flex items-center gap-1.5 border-t border-gray-100 dark:border-gray-700">
                                    <span>+ Tambah tujuan baru:</span>
                                    <span class="underline" x-text="tujuanVal"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                    @error('tujuan') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                        Nama Penerima (Admin Cabang / Penerima) <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" wire:model="nama_penerima" placeholder="Contoh: AYU"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2 text-sm text-gray-900 dark:text-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none" />
                    @error('nama_penerima') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- BAGIAN 2: FORM INPUT BARANG KELUAR (SESUAI REFERENSI GAMBAR KE-1) --}}
        <div class="relative z-20 rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-visible">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white">Barang Keluar</h3>
            </div>

            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-10 gap-y-5">
                    {{-- Kiri Atas: Nama Barang (Searchable Dropdown / Bisa Diketik Keyword) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-12 items-center gap-3">
                        <label class="sm:col-span-4 text-sm font-bold text-gray-800 dark:text-gray-200">
                            Nama Barang
                        </label>
                        <div class="sm:col-span-8"
                             x-data="{
                                 open: false,
                                 search: @entangle('input_nama_barang').live,
                                 selectedId: @entangle('selected_barang_id').live,
                                 items: @js(\App\Models\Barang::whereIn('status_racikan', ['Non Racikan', 'Sudah Diracik'])->orderBy('nama_barang')->get(['id', 'no', 'nama_barang', 'status_racikan', 'jumlah', 'satuan'])),
                                 get filteredItems() {
                                     if (!this.search || this.search.trim() === '') {
                                         return this.items;
                                     }
                                     const q = this.search.toLowerCase();
                                     return this.items.filter(i =>
                                         i.nama_barang.toLowerCase().includes(q) ||
                                         String(i.no).toLowerCase().includes(q)
                                     );
                                 },
                                 selectItem(item) {
                                     this.search = item.nama_barang;
                                     this.selectedId = item.id;
                                     this.open = false;
                                 }
                             }"
                             @click.outside="open = false">
                            <div class="relative">
                                <input type="text"
                                       x-model="search"
                                       @focus="open = true"
                                       @input="open = true"
                                       @keydown.escape.window="open = false"
                                       placeholder="--Pilih atau ketik keyword nama barang--"
                                       autocomplete="off"
                                       class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 pl-3.5 pr-9 py-2 text-sm text-gray-900 dark:text-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none" />

                                <button type="button"
                                        @click="open = !open"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                                    <x-filament::icon icon="heroicon-m-chevron-up-down" class="w-4 h-4" />
                                </button>

                                <div x-show="open"
                                     x-transition
                                     style="display: none;"
                                     class="absolute z-40 mt-1 w-full max-h-60 overflow-y-auto rounded-lg bg-white dark:bg-gray-800 shadow-lg border border-gray-200 dark:border-gray-700 py-1 text-sm">
                                    <template x-for="item in filteredItems" :key="item.id">
                                        <button type="button"
                                                @click="selectItem(item)"
                                                class="w-full text-left px-3.5 py-2 hover:bg-blue-50 dark:hover:bg-gray-700 flex items-center justify-between gap-2 transition-colors">
                                            <div>
                                                <span class="font-semibold text-gray-900 dark:text-white" x-text="item.nama_barang"></span>
                                                <span class="text-xs font-mono text-gray-400 ml-1" x-text="'[' + item.no + ']'"></span>
                                                <span class="text-[11px] font-semibold px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 ml-1"
                                                      x-text="item.status_racikan || 'Non Racikan'"></span>
                                            </div>
                                            <span class="text-xs font-semibold px-2 py-0.5 rounded bg-slate-100 dark:bg-gray-900 text-slate-700 dark:text-slate-300 shrink-0"
                                                  x-text="'Stok: ' + item.jumlah"></span>
                                        </button>
                                    </template>

                                    <div x-show="filteredItems.length === 0"
                                         class="px-3.5 py-2.5 text-xs text-gray-500 dark:text-gray-400 text-center">
                                        Barang tidak ditemukan di daftar.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Kanan Atas: No Barang / SKU + Tombol Search Pop-up --}}
                    <div class="grid grid-cols-1 sm:grid-cols-12 items-center gap-3">
                        <label class="sm:col-span-4 text-sm font-bold text-gray-800 dark:text-gray-200">
                            No Barang / SKU
                        </label>
                        <div class="sm:col-span-8 flex items-center gap-2">
                            <input type="text"
                                   wire:model="input_no_barang"
                                   wire:keydown.enter.prevent="cariByNoBarang"
                                   placeholder="Ketik No Barang lalu klik cari..."
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2 text-sm font-mono text-gray-900 dark:text-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none" />
                            <button type="button"
                                    wire:click="bukaModalBarang"
                                    title="Cari & Pilih dari Tabel Barang"
                                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 hover:bg-blue-700 text-white px-3.5 py-2.5 shadow-sm transition shrink-0">
                                <x-filament::icon icon="heroicon-m-magnifying-glass" class="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    {{-- Kiri Bawah: Expired Date --}}
                    <div class="grid grid-cols-1 sm:grid-cols-12 items-center gap-3">
                        <label class="sm:col-span-4 text-sm font-bold text-gray-800 dark:text-gray-200">
                            Expired Date
                        </label>
                        <div class="sm:col-span-8">
                            <input type="date"
                                   wire:model="input_expired"
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2 text-sm text-gray-900 dark:text-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none" />
                        </div>
                    </div>

                    {{-- Kanan Bawah: Jumlah Barang --}}
                    <div class="grid grid-cols-1 sm:grid-cols-12 items-center gap-3">
                        <label class="sm:col-span-4 text-sm font-bold text-gray-800 dark:text-gray-200">
                            Jumlah Barang
                        </label>
                        <div class="sm:col-span-8">
                            <input type="number"
                                   min="1"
                                   wire:model="input_jumlah"
                                   placeholder="Masukkan jumlah..."
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3.5 py-2 text-sm font-semibold text-gray-900 dark:text-white focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none" />
                            @if ($stok_tersedia > 0)
                                <span class="text-xs text-emerald-600 font-medium mt-1 block">
                                    Stok tersedia di gudang: {{ number_format($stok_tersedia, 0, ',', '.') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Tombol Batal & Tambah ke daftar bawah --}}
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button"
                            wire:click="resetItemInput"
                            class="rounded-md bg-gray-600 hover:bg-gray-700 px-4 py-2 text-xs font-semibold text-white shadow-xs transition">
                        Batal
                    </button>
                    <button type="button"
                            wire:click="tambahItemKeTabel"
                            class="rounded-md bg-blue-600 hover:bg-blue-700 px-4 py-2 text-xs font-semibold text-white shadow-xs transition">
                        Tambah
                    </button>
                </div>

                {{-- Tabel Daftar Item Barang yang akan dikeluarkan --}}
                <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 text-xs font-bold uppercase text-gray-700 dark:text-gray-300">
                                <th class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700 w-16">No</th>
                                <th class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700">Nama Barang</th>
                                <th class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700">No Barang / SKU</th>
                                <th class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700">ED (Expired)</th>
                                <th class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700 text-right">Jumlah</th>
                                <th class="py-2.5 px-4 text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($items as $idx => $item)
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-gray-800/50">
                                    <td class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700 font-mono text-xs font-bold">
                                        {{ $idx + 1 }}
                                    </td>
                                    <td class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700 font-semibold text-gray-900 dark:text-white">
                                        {{ $item['nama_barang'] }}
                                    </td>
                                    <td class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700 font-mono text-xs">
                                        {{ $item['no_barang'] }}
                                    </td>
                                    <td class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700 tabular-nums">
                                        {{ !empty($item['tanggal_expired']) ? \Carbon\Carbon::parse($item['tanggal_expired'])->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700 text-right font-bold tabular-nums">
                                        {{ number_format((int) $item['jumlah'], 0, ',', '.') }}
                                    </td>
                                    <td class="py-2.5 px-4 text-center">
                                        <button type="button"
                                                wire:click="hapusItemDariTabel({{ $idx }})"
                                                class="inline-flex items-center gap-1 rounded bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 px-2.5 py-1 text-xs font-semibold">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-gray-400 text-xs">
                                        Belum ada barang yang ditambahkan ke daftar pengeluaran.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Tombol Simpan Transaksi Utama --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                    <a href="{{ \App\Filament\Pages\BarangKeluar::getUrl() }}"
                       wire:navigate
                       class="rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 px-5 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-200 transition">
                        Kembali
                    </a>
                    <button type="button"
                            wire:click="simpanTransaksi"
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-600 hover:bg-blue-700 px-6 py-2.5 text-sm font-bold text-white shadow-sm transition">
                        <x-filament::icon icon="heroicon-m-check" class="w-4 h-4" />
                        <span>Simpan Barang Keluar</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL POP-UP LIST BARANG (SESUAI REFERENSI GAMBAR KE-2) --}}
    @if ($showModalBarang)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 overflow-y-auto">
            <div class="relative w-full max-w-4xl rounded-xl bg-white dark:bg-gray-900 shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden my-8">
                {{-- Header Modal --}}
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white">List Barang</h3>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Daftar stok yang tersedia</span>
                </div>

                {{-- Input Filter Pencarian Cepat di dalam Modal --}}
                <div class="px-6 pt-4 pb-2 flex items-center justify-between gap-4">
                    <input type="text"
                           wire:model.live.debounce.250ms="searchModal"
                           placeholder="Cari No Barang / SKU atau Nama Barang..."
                           class="w-full max-w-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2 text-sm text-gray-900 dark:text-white focus:border-blue-600 outline-none" />
                </div>

                {{-- Tabel Pop-up Barang --}}
                <div class="p-6 max-h-[60vh] overflow-y-auto">
                    <table class="w-full text-left border-collapse border border-gray-200 dark:border-gray-700 text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-200 font-bold border-b border-gray-200 dark:border-gray-700">
                                <th class="py-3 px-4 border-r border-gray-200 dark:border-gray-700 w-14">No</th>
                                <th class="py-3 px-4 border-r border-gray-200 dark:border-gray-700">
                                    <div>SKU / No Barang</div>
                                    <div class="text-xs font-semibold text-gray-600 dark:text-gray-400">Nama Barang</div>
                                </th>
                                <th class="py-3 px-4 border-r border-gray-200 dark:border-gray-700">Tanggal Expire</th>
                                <th class="py-3 px-4 border-r border-gray-200 dark:border-gray-700">Satuan</th>
                                <th class="py-3 px-4 border-r border-gray-200 dark:border-gray-700">Kategori</th>
                                <th class="py-3 px-4 border-r border-gray-200 dark:border-gray-700 text-right">Sisa</th>
                                <th class="py-3 px-4 text-center w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($this->daftarBarang as $idx => $brg)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/60">
                                    <td class="py-3 px-4 border-r border-gray-200 dark:border-gray-700 align-top">
                                        {{ $idx + 1 }}
                                    </td>
                                    <td class="py-3 px-4 border-r border-gray-200 dark:border-gray-700">
                                        <div class="text-xs font-mono font-semibold text-gray-600 dark:text-gray-400">{{ $brg->no }}</div>
                                        <div class="font-medium text-gray-900 dark:text-white mt-0.5">{{ $brg->nama_barang }}</div>
                                    </td>
                                    <td class="py-3 px-4 border-r border-gray-200 dark:border-gray-700 tabular-nums">
                                        {{ $brg->tanggal_expired ? $brg->tanggal_expired->format('Y-m-d') : '-' }}
                                    </td>
                                    <td class="py-3 px-4 border-r border-gray-200 dark:border-gray-700">
                                        {{ strtolower($brg->satuan ?? 'pcs') }}
                                    </td>
                                    <td class="py-3 px-4 border-r border-gray-200 dark:border-gray-700">
                                        {{ $brg->kategori ?? '-' }}
                                    </td>
                                    <td class="py-3 px-4 border-r border-gray-200 dark:border-gray-700 text-right font-semibold tabular-nums">
                                        {{ number_format($brg->jumlah, 0, ',', ',') }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <button type="button"
                                                wire:click="pilihBarangDariModal({{ $brg->id }})"
                                                class="rounded-md bg-blue-600 hover:bg-blue-700 px-3.5 py-1.5 text-xs font-bold text-white shadow-xs transition">
                                            Pilih
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-6 text-center text-gray-400 text-xs">
                                        Tidak ada barang yang ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Footer Modal --}}
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800 flex items-center justify-start">
                    <button type="button"
                            wire:click="tutupModalBarang"
                            class="rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
