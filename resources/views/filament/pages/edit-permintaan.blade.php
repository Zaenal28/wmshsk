<x-filament-panels::page>
    <div class="mb-2 flex items-center justify-between">
        <a href="{{ \App\Filament\Pages\KelolaPermintaan::getUrl() }}" wire:navigate
           class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400">
            <x-filament::icon icon="heroicon-m-arrow-left" class="w-4 h-4" />
            Kembali ke Data Permintaan Barang
        </a>
    </div>

    <div class="space-y-6 antialiased">
        {{-- BAGIAN 1: FORM HEADER PERMINTAAN --}}
        <div class="relative z-30 rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-visible">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 bg-slate-50/60 dark:bg-gray-800/40 rounded-t-xl">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Edit Surat Permintaan Barang</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Perbarui data surat permintaan barang cabang.</p>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                {{-- Tanggal Permintaan --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                        Tanggal Permintaan <span class="text-rose-600">*</span>
                    </label>
                    <input type="date" wire:model="tanggal_permintaan"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2 text-sm text-gray-900 dark:text-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none" />
                    @error('tanggal_permintaan') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- No Surat --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                        No Surat <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" wire:model="no_surat"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2 text-sm font-mono font-bold text-gray-900 dark:text-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none" />
                    @error('no_surat') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Cabang --}}
                <div x-data="{
                        openCabang: false,
                        filterQuery: '',
                        cabangVal: @entangle('cabang').live,
                        options: @js(\App\Models\PermintaanBarang::getCabangList()),
                        get filteredOptions() {
                            if (!this.filterQuery || this.filterQuery.trim() === '') {
                                return this.options;
                            }
                            const q = this.filterQuery.toLowerCase();
                            return this.options.filter(o => o.toLowerCase().includes(q));
                        },
                        get canAddNew() {
                            const text = (this.cabangVal || '').trim();
                            if (!text) return false;
                            const q = text.toLowerCase();
                            return !this.options.some(o => o.toLowerCase() === q);
                        },
                        selectCabang(val) {
                            this.cabangVal = val;
                            this.filterQuery = '';
                            this.openCabang = false;
                        },
                        addNewCabang() {
                            const newVal = (this.cabangVal || '').trim();
                            if (newVal && !this.options.includes(newVal)) {
                                this.options.push(newVal);
                            }
                            this.cabangVal = newVal;
                            this.filterQuery = '';
                            this.openCabang = false;
                        }
                    }"
                    @click.outside="openCabang = false">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                        Cabang <span class="text-rose-600">*</span>
                    </label>
                    <div class="relative">
                        <input type="text"
                               x-model="cabangVal"
                               @focus="filterQuery = ''; openCabang = true"
                               @click="filterQuery = ''; openCabang = true"
                               @input="filterQuery = $event.target.value; openCabang = true"
                               @keydown.escape.window="openCabang = false"
                               placeholder="--Pilih atau ketik cabang--"
                               autocomplete="off"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 pl-3.5 pr-9 py-2 text-sm text-gray-900 dark:text-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none" />

                        <button type="button"
                                @click="filterQuery = ''; openCabang = !openCabang"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                            <x-filament::icon icon="heroicon-m-chevron-up-down" class="w-4 h-4" />
                        </button>

                        <div x-show="openCabang"
                             x-transition
                             style="display: none;"
                             class="absolute z-50 mt-1 w-full max-h-60 overflow-y-auto rounded-lg bg-white dark:bg-gray-800 shadow-xl border border-gray-200 dark:border-gray-700 py-1 text-sm">
                            <template x-for="opt in filteredOptions" :key="opt">
                                <button type="button"
                                        @click="selectCabang(opt)"
                                        class="w-full text-left px-3.5 py-2 hover:bg-blue-50 dark:hover:bg-gray-700 flex items-center justify-between text-gray-900 dark:text-white font-medium transition-colors">
                                    <span x-text="opt"></span>
                                </button>
                            </template>

                            <template x-if="canAddNew">
                                <button type="button"
                                        @click="addNewCabang()"
                                        class="w-full text-left px-3.5 py-2 bg-blue-50/70 hover:bg-blue-100 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-300 font-semibold flex items-center gap-1.5 border-t border-gray-100 dark:border-gray-700">
                                    <span>+ Tambah cabang baru:</span>
                                    <span class="underline" x-text="cabangVal"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                    @error('cabang') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- PIC --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                        PIC <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" wire:model="pic" placeholder="Nama PIC"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2 text-sm text-gray-900 dark:text-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none" />
                    @error('pic') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- BAGIAN 2: FORM INPUT BARANG --}}
        <div class="relative z-20 rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-visible">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white">Form Input Barang</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Pilih atau ketik nama barang serta jumlah yang diminta.</p>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-end">
                    <div class="lg:col-span-5"
                         x-data="{
                             open: false,
                             search: @entangle('input_nama_barang').live,
                             selectedId: @entangle('selected_barang_id').live,
                             items: @js(\App\Models\Barang::orderBy('nama_barang')->pluck('nama_barang')),
                             get filteredItems() {
                                 if (!this.search || this.search.trim() === '') {
                                     return this.items;
                                 }
                                 const q = this.search.toLowerCase();
                                 return this.items.filter(name => name.toLowerCase().includes(q));
                             },
                             selectItem(name) {
                                 this.search = name;
                                 this.open = false;
                             }
                         }"
                         @click.outside="open = false">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                            Nama Barang <span class="text-rose-600">*</span>
                        </label>
                        <div class="relative flex items-center gap-2">
                            <div class="relative w-full">
                                <input type="text"
                                       x-model="search"
                                       @focus="open = true"
                                       @input="open = true"
                                       @keydown.escape.window="open = false"
                                       placeholder="--Pilih atau ketik nama barang--"
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
                                    <template x-for="name in filteredItems" :key="name">
                                        <button type="button"
                                                @click="selectItem(name)"
                                                class="w-full text-left px-3.5 py-2 hover:bg-blue-50 dark:hover:bg-gray-700 flex items-center justify-between transition-colors">
                                            <span class="font-semibold text-gray-900 dark:text-white" x-text="name"></span>
                                        </button>
                                    </template>

                                    <div x-show="filteredItems.length === 0"
                                         class="px-3.5 py-2.5 text-xs text-gray-500 dark:text-gray-400 text-center">
                                        Gunakan nama yang Anda ketik jika barang tidak ada di master.
                                    </div>
                                </div>
                            </div>

                            <button type="button"
                                    wire:click="bukaModalBarang"
                                    title="Pilih dari daftar Master Barang"
                                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 hover:bg-blue-700 text-white px-3.5 py-2.5 shadow-sm transition shrink-0">
                                <x-filament::icon icon="heroicon-m-magnifying-glass" class="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    <div class="lg:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                            Jumlah <span class="text-rose-600">*</span>
                        </label>
                        <input type="number"
                               min="1"
                               wire:model="input_jumlah"
                               placeholder="Jumlah..."
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2 text-sm font-semibold text-gray-900 dark:text-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none" />
                    </div>

                    <div class="lg:col-span-3">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                            Keterangan
                        </label>
                        <input type="text"
                               wire:model="input_keterangan"
                               wire:keydown.enter.prevent="tambahItemKeTabel"
                               placeholder="Contoh: Stok menipis / Emergency"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2 text-sm text-gray-900 dark:text-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none" />
                    </div>

                    <div class="lg:col-span-2 flex items-center gap-2">
                        <button type="button"
                                wire:click="tambahItemKeTabel"
                                class="w-full rounded-lg {{ $editingIndex >= 0 ? 'bg-amber-600 hover:bg-amber-700' : 'bg-blue-600 hover:bg-blue-700' }} px-4 py-2.5 text-sm font-bold text-white shadow-sm transition">
                            {{ $editingIndex >= 0 ? 'Update Item' : '+ Tambah' }}
                        </button>
                        <button type="button"
                                wire:click="resetItemInput"
                                class="rounded-lg bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 px-3 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 transition">
                            Reset
                        </button>
                    </div>
                </div>

                {{-- TABLE BARANG (|no|nama barang|jumlah|keterangan|) --}}
                <div class="mt-4">
                    <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-2">Tabel Barang</h4>
                    <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 text-xs font-bold uppercase text-gray-700 dark:text-gray-300">
                                    <th class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700 w-16">No</th>
                                    <th class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700">Nama Barang</th>
                                    <th class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700 text-right w-28">Jumlah</th>
                                    <th class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700">Keterangan</th>
                                    <th class="py-2.5 px-4 text-center w-36">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($items as $idx => $item)
                                    <tr class="{{ $editingIndex === $idx ? 'bg-amber-50 dark:bg-amber-950/30 ring-1 ring-amber-400' : 'hover:bg-slate-50/80 dark:hover:bg-gray-800/50' }}">
                                        <td class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700 font-mono text-xs font-bold text-center">
                                            {{ $idx + 1 }}
                                        </td>
                                        <td class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700 font-semibold text-gray-900 dark:text-white">
                                            {{ $item['nama_barang'] }}
                                        </td>
                                        <td class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700 text-right font-bold tabular-nums">
                                            {{ number_format((int) $item['jumlah'], 0, ',', '.') }}
                                        </td>
                                        <td class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 text-xs">
                                            {{ !empty($item['keterangan']) ? $item['keterangan'] : '-' }}
                                        </td>
                                        <td class="py-2.5 px-4 text-center">
                                            <div class="inline-flex items-center gap-1.5 justify-center">
                                                <button type="button"
                                                        wire:click="editItemDariTabel({{ $idx }})"
                                                        class="inline-flex items-center gap-1 rounded bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 px-2.5 py-1 text-xs font-semibold transition">
                                                    Edit
                                                </button>
                                                <button type="button"
                                                        wire:click="hapusItemDariTabel({{ $idx }})"
                                                        class="inline-flex items-center gap-1 rounded bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 px-2.5 py-1 text-xs font-semibold transition">
                                                    Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-6 text-center text-gray-400 text-xs">
                                            Belum ada barang yang ditambahkan ke tabel.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if (!empty($items))
                                <tfoot>
                                    <tr class="bg-slate-100 dark:bg-gray-800 font-bold">
                                        <td colspan="2" class="py-2.5 px-4 text-right uppercase text-xs">Total Jumlah Barang:</td>
                                        <td class="py-2.5 px-4 text-right tabular-nums text-blue-600 dark:text-blue-400">
                                            {{ number_format(collect($items)->sum('jumlah'), 0, ',', '.') }}
                                        </td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>

                {{-- Tombol Update Utama --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                    <a href="{{ \App\Filament\Pages\KelolaPermintaan::getUrl() }}"
                       wire:navigate
                       class="rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 px-5 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-200 transition">
                        Batal
                    </a>
                    <button type="button"
                            wire:click="updatePermintaan"
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-600 hover:bg-blue-700 px-6 py-2.5 text-sm font-bold text-white shadow-sm transition">
                        <x-filament::icon icon="heroicon-m-check" class="w-4 h-4" />
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL POP-UP LIST BARANG --}}
    @if ($showModalBarang)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 overflow-y-auto">
            <div class="relative w-full max-w-4xl rounded-xl bg-white dark:bg-gray-900 shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden my-8">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white">Daftar Master Barang</h3>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Pilih salah satu barang untuk dimasukkan ke form permintaan</span>
                    </div>
                    <button type="button" wire:click="tutupModalBarang" class="text-gray-400 hover:text-gray-600">
                        <x-filament::icon icon="heroicon-m-x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <div class="px-6 pt-4 pb-2">
                    <input type="text"
                           wire:model.live.debounce.250ms="searchModal"
                           placeholder="Cari SKU atau Nama Barang..."
                           class="w-full max-w-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2 text-sm text-gray-900 dark:text-white focus:border-blue-600 outline-none" />
                </div>

                <div class="p-6 max-h-[60vh] overflow-y-auto">
                    <table class="w-full text-left border-collapse border border-gray-200 dark:border-gray-700 text-sm">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-800 text-xs font-bold uppercase text-gray-700 dark:text-gray-300">
                                <th class="p-3 border border-gray-200 dark:border-gray-700 w-16">No</th>
                                <th class="p-3 border border-gray-200 dark:border-gray-700">Nama Barang</th>
                                <th class="p-3 border border-gray-200 dark:border-gray-700">Kategori</th>
                                <th class="p-3 border border-gray-200 dark:border-gray-700 text-center w-24">Pilih</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($this->daftarBarang as $b)
                                <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-800/60">
                                    <td class="p-3 border border-gray-200 dark:border-gray-700 font-mono text-xs">{{ $b->no }}</td>
                                    <td class="p-3 border border-gray-200 dark:border-gray-700 font-semibold">{{ $b->nama_barang }}</td>
                                    <td class="p-3 border border-gray-200 dark:border-gray-700 text-xs text-gray-500">{{ $b->kategori ?: '-' }}</td>
                                    <td class="p-3 border border-gray-200 dark:border-gray-700 text-center">
                                        <button type="button"
                                                wire:click="pilihBarangDariModal({{ $b->id }})"
                                                class="rounded bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 text-xs font-bold transition">
                                            Pilih
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-xs text-gray-400">Tidak ada barang yang cocok.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/40 flex justify-end">
                    <button type="button" wire:click="tutupModalBarang"
                            class="rounded-lg bg-gray-200 dark:bg-gray-700 px-4 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
