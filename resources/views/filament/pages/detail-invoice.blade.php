<x-filament-panels::page>
    <div class="mb-2 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ \App\Filament\Pages\KelolaInvoice::getUrl() }}" wire:navigate
           class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400">
            <x-filament::icon icon="heroicon-m-arrow-left" class="w-4 h-4" />
            Kembali ke Tabel Kelola Invoice
        </a>

        <div class="flex items-center gap-2">
            <a href="{{ route('kelola-invoice.pdf', ['record' => $invoice->id]) }}"
               target="_blank"
               class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 px-4 py-2 text-xs font-semibold text-white shadow-sm transition">
                <x-filament::icon icon="heroicon-m-printer" class="w-4 h-4" />
                <span>Cetak PDF Invoice</span>
            </a>

            <a href="{{ route('kelola-invoice.excel', ['record' => $invoice->id]) }}"
               class="inline-flex items-center gap-1.5 rounded-lg bg-teal-600 hover:bg-teal-700 px-4 py-2 text-xs font-semibold text-white shadow-sm transition">
                <x-filament::icon icon="heroicon-m-table-cells" class="w-4 h-4" />
                <span>Cetak Excel</span>
            </a>

            <a href="{{ \App\Filament\Pages\EditInvoice::getUrl(['record' => $invoice->id]) }}"
               wire:navigate
               class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 px-4 py-2 text-xs font-semibold text-white shadow-sm transition">
                <x-filament::icon icon="heroicon-m-pencil-square" class="w-4 h-4" />
                <span>Edit Invoice</span>
            </a>
        </div>
    </div>

    <div class="space-y-6 antialiased">
        {{-- BAGIAN 1: DATA FORM INVOICE YANG DIINPUT --}}
        <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 bg-slate-50/60 dark:bg-gray-800/40">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Informasi Form Invoice</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Detail parameter penarikan data barang keluar untuk Sales Invoice ini.</p>
            </div>

            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <div class="p-4 rounded-lg bg-slate-50 dark:bg-gray-800/60 border border-slate-200/70 dark:border-gray-700">
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-500">Tujuan (Bill To)</div>
                    <div class="mt-1 text-base font-bold text-gray-900 dark:text-white">{{ $invoice->tujuan }}</div>
                </div>

                <div class="p-4 rounded-lg bg-slate-50 dark:bg-gray-800/60 border border-slate-200/70 dark:border-gray-700">
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-500">Nama Tempat / Alamat</div>
                    <div class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $invoice->nama_tempat ?: '-' }}</div>
                </div>

                <div class="p-4 rounded-lg bg-slate-50 dark:bg-gray-800/60 border border-slate-200/70 dark:border-gray-700">
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-500">No Invoice</div>
                    <div class="mt-1 text-base font-mono font-bold text-blue-600 dark:text-blue-400">{{ $invoice->no_invoice }}</div>
                </div>

                <div class="p-4 rounded-lg bg-slate-50 dark:bg-gray-800/60 border border-slate-200/70 dark:border-gray-700">
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-500">Tanggal Invoice</div>
                    <div class="mt-1 text-base font-semibold tabular-nums text-gray-900 dark:text-white">
                        {{ $invoice->tanggal_invoice ? $invoice->tanggal_invoice->format('d/m/Y') : '-' }}
                    </div>
                </div>

                <div class="p-4 rounded-lg bg-slate-50 dark:bg-gray-800/60 border border-slate-200/70 dark:border-gray-700">
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-500">Dari Tanggal</div>
                    <div class="mt-1 text-base font-semibold tabular-nums text-gray-900 dark:text-white">
                        {{ $invoice->dari_tanggal ? $invoice->dari_tanggal->format('d/m/Y') : '-' }}
                    </div>
                </div>

                <div class="p-4 rounded-lg bg-slate-50 dark:bg-gray-800/60 border border-slate-200/70 dark:border-gray-700">
                    <div class="text-xs font-semibold uppercase tracking-wider text-gray-500">Ke Tanggal</div>
                    <div class="mt-1 text-base font-semibold tabular-nums text-gray-900 dark:text-white">
                        {{ $invoice->ke_tanggal ? $invoice->ke_tanggal->format('d/m/Y') : '-' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- BAGIAN 2: TABEL BARANG KELUAR YANG DITARIK KE INVOICE INI --}}
        <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">
                        Daftar Barang Keluar (Tujuan: {{ $invoice->tujuan }})
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Rincian item barang keluar dan kalkulasi harga pada periode {{ $invoice->periode_transaksi }}.
                    </p>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 text-xs font-bold uppercase text-gray-700 dark:text-gray-300">
                                <th class="py-3 px-4 border-r border-gray-200 dark:border-gray-700 w-14 text-center">No</th>
                                <th class="py-3 px-4 border-r border-gray-200 dark:border-gray-700">Item Description</th>
                                <th class="py-3 px-4 border-r border-gray-200 dark:border-gray-700 text-center">No. Surat Jalan</th>
                                <th class="py-3 px-4 border-r border-gray-200 dark:border-gray-700 text-center">Tgl Keluar</th>
                                <th class="py-3 px-4 border-r border-gray-200 dark:border-gray-700 text-center">QTY</th>
                                <th class="py-3 px-4 border-r border-gray-200 dark:border-gray-700 text-center">Item Unit</th>
                                <th class="py-3 px-4 border-r border-gray-200 dark:border-gray-700 text-right">Unit Price</th>
                                <th class="py-3 px-4 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($invoice->items as $idx => $item)
                                <tr class="hover:bg-slate-50/70 dark:hover:bg-gray-800/40">
                                    <td class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700 text-center font-mono text-xs font-bold">
                                        {{ $idx + 1 }}
                                    </td>
                                    <td class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700 font-semibold text-gray-900 dark:text-white">
                                        {{ $item->item_description }}
                                    </td>
                                    <td class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700 text-center font-mono text-xs">
                                        {{ $item->no_surat_jalan ?: '-' }}
                                    </td>
                                    <td class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700 text-center tabular-nums">
                                        {{ $item->tanggal_keluar ? $item->tanggal_keluar->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700 text-center font-bold tabular-nums">
                                        {{ number_format($item->qty, 0, ',', ',') }}
                                    </td>
                                    <td class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700 text-center">
                                        {{ $item->item_unit }}
                                    </td>
                                    <td class="py-2.5 px-4 border-r border-gray-200 dark:border-gray-700 text-right tabular-nums">
                                        Rp{{ number_format((float) $item->unit_price, 0, ',', ',') }}
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-bold tabular-nums text-gray-900 dark:text-white">
                                        Rp{{ number_format((float) $item->amount, 0, ',', ',') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-6 text-center text-gray-400 text-xs">
                                        Tidak ada item barang pada invoice ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end">
                    <div class="w-full max-w-md rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-2.5 bg-slate-50/70 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700 text-sm">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">Sub Total</span>
                            <span class="font-bold tabular-nums text-gray-900 dark:text-white">
                                Rp{{ number_format((float) $invoice->sub_total, 0, ',', ',') }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between px-4 py-2.5 bg-slate-50/70 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700 text-sm">
                            <span class="font-semibold text-amber-700 dark:text-amber-400">Discount 20%</span>
                            <span class="font-bold tabular-nums text-amber-700 dark:text-amber-400">
                                Rp{{ number_format((float) $invoice->diskon_nominal, 0, ',', ',') }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between px-4 py-3 bg-blue-50/60 dark:bg-blue-900/20 text-base">
                            <span class="font-bold text-gray-900 dark:text-white">Total</span>
                            <span class="font-extrabold tabular-nums text-blue-700 dark:text-blue-400">
                                Rp{{ number_format((float) $invoice->total_tagihan, 0, ',', ',') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
