<x-filament-panels::page>
    <div class="mb-2 flex items-center justify-between">
        <a href="{{ \App\Filament\Pages\KelolaInvoice::getUrl() }}" wire:navigate
           class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400">
            <x-filament::icon icon="heroicon-m-arrow-left" class="w-4 h-4" />
            Kembali ke Tabel Kelola Invoice
        </a>
    </div>

    <div class="space-y-6 antialiased">
        {{-- BAGIAN 1: FORM INPUT TAMBAH / EDIT INVOICE --}}
        <div class="relative z-30 rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-visible">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 bg-slate-50/60 dark:bg-gray-800/40 rounded-t-xl">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Form Input Data Invoice</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Pilih Tujuan (dari data Barang Keluar) dan rentang tanggal, lalu klik tombol <strong>Tarik Data Barang Keluar</strong>.
                </p>
            </div>

            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    {{-- 1. Tujuan (Dropdown dari Tujuan Barang Keluar) --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                            Tujuan (Bill To) <span class="text-rose-600">*</span>
                        </label>
                        <select wire:model.live="tujuan"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2 text-sm text-gray-900 dark:text-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none">
                            <option value="">-- Pilih Tujuan Barang Keluar --</option>
                            @foreach (\App\Models\BarangKeluar::getTujuanList() as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                        @error('tujuan') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- 2. Nama Tempat / Alamat Tujuan (Dropdown List + Bisa Ketik/Tambah Manual) --}}
                    <div x-data="{
                            openAlamat: false,
                            filterAlamat: '',
                            alamatVal: @entangle('nama_tempat').live,
                            options: @js(\App\Models\Invoice::getAlamatList()),
                            get filteredOptions() {
                                if (!this.filterAlamat || this.filterAlamat.trim() === '') {
                                    return this.options;
                                }
                                const q = this.filterAlamat.toLowerCase();
                                return this.options.filter(o => o.toLowerCase().includes(q));
                            },
                            get canAddNew() {
                                const text = (this.alamatVal || '').trim();
                                if (!text) return false;
                                const q = text.toLowerCase();
                                return !this.options.some(o => o.toLowerCase() === q);
                            },
                            selectAlamat(val) {
                                this.alamatVal = val;
                                this.filterAlamat = '';
                                this.openAlamat = false;
                            },
                            addNewAlamat() {
                                const newVal = (this.alamatVal || '').trim();
                                if (newVal && !this.options.includes(newVal)) {
                                    this.options.push(newVal);
                                }
                                this.alamatVal = newVal;
                                this.filterAlamat = '';
                                this.openAlamat = false;
                            }
                        }"
                        @click.outside="openAlamat = false">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                            Nama Tempat / Alamat <span class="text-rose-600">*</span>
                        </label>
                        <div class="relative">
                            <input type="text"
                                   x-model="alamatVal"
                                   @focus="filterAlamat = ''; openAlamat = true"
                                   @click="filterAlamat = ''; openAlamat = true"
                                   @input="filterAlamat = $event.target.value; openAlamat = true"
                                   @keydown.escape.window="openAlamat = false"
                                   placeholder="--Pilih alamat dari list atau ketik manual--"
                                   autocomplete="off"
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 pl-3.5 pr-9 py-2 text-sm text-gray-900 dark:text-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none" />

                            <button type="button"
                                    @click="filterAlamat = ''; openAlamat = !openAlamat"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                                <x-filament::icon icon="heroicon-m-chevron-up-down" class="w-4 h-4" />
                            </button>

                            <div x-show="openAlamat"
                                 x-transition
                                 style="display: none;"
                                 class="absolute z-50 mt-1 w-full max-h-64 overflow-y-auto rounded-lg bg-white dark:bg-gray-800 shadow-xl border border-gray-200 dark:border-gray-700 py-1 text-sm">
                                <template x-for="opt in filteredOptions" :key="opt">
                                    <button type="button"
                                            @click="selectAlamat(opt)"
                                            class="w-full text-left px-3.5 py-2.5 hover:bg-blue-50 dark:hover:bg-gray-700 text-gray-900 dark:text-white text-xs leading-relaxed font-medium border-b border-gray-100 dark:border-gray-700/50 last:border-0 transition-colors">
                                        <span x-text="opt"></span>
                                    </button>
                                </template>

                                <template x-if="canAddNew">
                                    <button type="button"
                                            @click="addNewAlamat()"
                                            class="w-full text-left px-3.5 py-2.5 bg-blue-50/70 hover:bg-blue-100 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-300 text-xs font-semibold flex items-center gap-1.5 border-t border-gray-100 dark:border-gray-700">
                                        <span>+ Gunakan / tambah alamat baru:</span>
                                        <span class="underline" x-text="alamatVal"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        @error('nama_tempat') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- 3. No Invoice --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                            No Invoice <span class="text-rose-600">*</span>
                        </label>
                        <input type="text"
                               wire:model="no_invoice"
                               placeholder="Contoh: INV/26/HBA-V/003"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2 text-sm font-mono text-gray-900 dark:text-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none" />
                        @error('no_invoice') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- 4. Dari Tanggal --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                            Dari Tanggal <span class="text-rose-600">*</span>
                        </label>
                        <input type="date"
                               wire:model="dari_tanggal"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2 text-sm text-gray-900 dark:text-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none" />
                        @error('dari_tanggal') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- 5. Ke Tanggal --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                            Ke Tanggal <span class="text-rose-600">*</span>
                        </label>
                        <input type="date"
                               wire:model="ke_tanggal"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2 text-sm text-gray-900 dark:text-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none" />
                        @error('ke_tanggal') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- 6. Tanggal Cetak Invoice --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                            Tanggal Invoice
                        </label>
                        <input type="date"
                               wire:model="tanggal_invoice"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-2 text-sm text-gray-900 dark:text-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none" />
                    </div>
                </div>

                {{-- Tombol Tarik Data Barang Keluar --}}
                <div class="pt-2 flex items-center justify-end border-t border-gray-100 dark:border-gray-800">
                    <button type="button"
                            wire:click="tarikDataBarangKeluar"
                            class="inline-flex items-center gap-2 rounded-lg bg-amber-500 hover:bg-amber-600 active:bg-amber-700 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition">
                        <x-filament::icon icon="heroicon-m-arrow-down-tray" class="w-4 h-4" />
                        <span>Tarik Data Barang Keluar</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- BAGIAN 2: TABEL KALKULASI INVOICE OTOMATIS --}}
        <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Tabel Kalkulasi Invoice Otomatis</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Daftar barang keluar yang ditarik berdasarkan Tujuan & Periode (harga satuan dapat disesuaikan langsung jika diperlukan).
                    </p>
                </div>

                <span class="inline-flex items-center rounded-md bg-blue-50 dark:bg-blue-900/30 px-3 py-1 text-xs font-bold text-blue-700 dark:text-blue-300">
                    Total Baris: {{ count($items) }} Item
                </span>
            </div>

            <div class="p-6 space-y-6">
                <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 text-xs font-bold uppercase text-gray-700 dark:text-gray-300">
                                <th class="py-3 px-4 border-r border-gray-200 dark:border-gray-700 w-14 text-center">No</th>
                                <th class="py-3 px-4 border-r border-gray-200 dark:border-gray-700">Item Description</th>
                                <th class="py-3 px-4 border-r border-gray-200 dark:border-gray-700 w-28 text-center">QTY</th>
                                <th class="py-3 px-4 border-r border-gray-200 dark:border-gray-700 w-28 text-center">Item Unit</th>
                                <th class="py-3 px-4 border-r border-gray-200 dark:border-gray-700 w-44 text-right">Unit Price (Rp)</th>
                                <th class="py-3 px-4 border-r border-gray-200 dark:border-gray-700 w-44 text-right">Amount (Rp)</th>
                                <th class="py-3 px-4 w-24 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($items as $idx => $item)
                                <tr class="hover:bg-slate-50/70 dark:hover:bg-gray-800/40">
                                    <td class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700 text-center font-mono text-xs font-bold">
                                        {{ $idx + 1 }}
                                    </td>
                                    <td class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700">
                                        <input type="text"
                                               wire:model.live="items.{{ $idx }}.item_description"
                                               class="w-full rounded border border-gray-200 dark:border-gray-700 bg-transparent px-2.5 py-1 text-sm font-semibold text-gray-900 dark:text-white focus:border-blue-600 outline-none" />
                                        @if (!empty($item['no_surat_jalan']))
                                            <div class="text-[11px] text-gray-400 mt-0.5 font-mono">
                                                Ref SJ: {{ $item['no_surat_jalan'] }} ({{ !empty($item['tanggal_keluar']) ? \Carbon\Carbon::parse($item['tanggal_keluar'])->format('d/m/Y') : '-' }})
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700 text-center">
                                        <input type="number"
                                               min="1"
                                               wire:model.live="items.{{ $idx }}.qty"
                                               class="w-20 text-center rounded border border-gray-200 dark:border-gray-700 bg-transparent px-2 py-1 text-sm font-bold tabular-nums text-gray-900 dark:text-white focus:border-blue-600 outline-none" />
                                    </td>
                                    <td class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700 text-center">
                                        <input type="text"
                                               wire:model.live="items.{{ $idx }}.item_unit"
                                               class="w-20 text-center rounded border border-gray-200 dark:border-gray-700 bg-transparent px-2 py-1 text-sm text-gray-700 dark:text-gray-200 focus:border-blue-600 outline-none" />
                                    </td>
                                    <td class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700 text-right">
                                        <input type="number"
                                               min="0"
                                               wire:model.live="items.{{ $idx }}.unit_price"
                                               class="w-36 text-right rounded border border-gray-200 dark:border-gray-700 bg-transparent px-2.5 py-1 text-sm font-semibold tabular-nums text-gray-900 dark:text-white focus:border-blue-600 outline-none" />
                                    </td>
                                    <td class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700 text-right font-bold tabular-nums text-gray-900 dark:text-white">
                                        Rp{{ number_format((float) ($item['amount'] ?? 0), 0, ',', ',') }}
                                    </td>
                                    <td class="py-2.5 px-4 text-center">
                                        <button type="button"
                                                wire:click="hapusBarisItem({{ $idx }})"
                                                class="inline-flex items-center rounded bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 px-2.5 py-1 text-xs font-semibold">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-gray-400 text-xs">
                                        Belum ada data barang. Pilih <strong>Tujuan</strong> dan <strong>Periode Tanggal</strong> di atas lalu klik <strong>Tarik Data Barang Keluar</strong>.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- FOOTER REKAP OTOMATIS: SUB TOTAL, DISKON 20%, TOTAL --}}
                <div class="flex justify-end">
                    <div class="w-full max-w-md rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-2.5 bg-slate-50/70 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700 text-sm">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">Sub Total</span>
                            <span class="font-bold tabular-nums text-gray-900 dark:text-white">
                                Rp{{ number_format($this->subTotal, 0, ',', ',') }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between px-4 py-2.5 bg-slate-50/70 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700 text-sm">
                            <span class="font-semibold text-amber-700 dark:text-amber-400">Discount 20%</span>
                            <span class="font-bold tabular-nums text-amber-700 dark:text-amber-400">
                                Rp{{ number_format($this->diskonNominal, 0, ',', ',') }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between px-4 py-3 bg-blue-50/60 dark:bg-blue-900/20 text-base">
                            <span class="font-bold text-gray-900 dark:text-white">Total</span>
                            <span class="font-extrabold tabular-nums text-blue-700 dark:text-blue-400">
                                Rp{{ number_format($this->totalTagihan, 0, ',', ',') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Tombol Simpan Invoice --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                    <a href="{{ \App\Filament\Pages\KelolaInvoice::getUrl() }}"
                       wire:navigate
                       class="rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 px-5 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-200 transition">
                        Batal
                    </a>
                    <button type="button"
                            wire:click="simpanInvoice"
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-600 hover:bg-blue-700 px-6 py-2.5 text-sm font-bold text-white shadow-sm transition">
                        <x-filament::icon icon="heroicon-m-check" class="w-4 h-4" />
                        <span>Simpan Invoice</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
