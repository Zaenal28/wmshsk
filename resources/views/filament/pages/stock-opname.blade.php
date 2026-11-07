<x-filament-panels::page>
    @include('filament.components.datatables-assets')

    <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden" wire:ignore>
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Riwayat & Sesi Stock Opname (DataTables.net)</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Perbandingan stok sistem vs stok fisik aktual di Gudang Pusat.</p>
            </div>
            <span class="inline-flex items-center rounded-md bg-blue-50 dark:bg-blue-900/30 px-2.5 py-1 text-xs font-medium text-blue-700 dark:text-blue-400 ring-1 ring-inset ring-blue-700/10">
                DataTables v2.1.8 Active
            </span>
        </div>

        <div class="p-2 overflow-x-auto">
            <table id="table-stock-opname" class="display w-full text-left">
                <thead>
                    <tr>
                        <th>Kode Opname</th>
                        <th>Tanggal Audit</th>
                        <th>Area / Zona Rak</th>
                        <th>Stok Sistem</th>
                        <th>Stok Fisik</th>
                        <th>Selisih</th>
                        <th>Auditor</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="font-mono font-medium">SO-202611-A01</td>
                        <td>05/11/2026</td>
                        <td>Zona A - Rak Packaging</td>
                        <td>4.820</td>
                        <td>4.820</td>
                        <td class="text-emerald-600 font-semibold">0</td>
                        <td>Gudang Pusat</td>
                        <td><span class="px-2 py-0.5 text-xs rounded-full bg-emerald-100 text-emerald-700">Sesuai / Approved</span></td>
                    </tr>
                    <tr>
                        <td class="font-mono font-medium">SO-202611-B01</td>
                        <td>06/11/2026</td>
                        <td>Zona B - Rak Label & Stiker</td>
                        <td>895</td>
                        <td>890</td>
                        <td class="text-rose-600 font-semibold">-5</td>
                        <td>Gudang Pusat</td>
                        <td><span class="px-2 py-0.5 text-xs rounded-full bg-amber-100 text-amber-700">Menunggu Approval</span></td>
                    </tr>
                    <tr>
                        <td class="font-mono font-medium">SO-202611-C02</td>
                        <td>07/11/2026</td>
                        <td>Zona C - Barang Fast Moving</td>
                        <td>12.400</td>
                        <td>12.400</td>
                        <td class="text-emerald-600 font-semibold">0</td>
                        <td>Gudang Pusat</td>
                        <td><span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700">Sedang Dihitung</span></td>
                    </tr>
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
                const el = document.getElementById('table-stock-opname');
                if (!el) return;
                if (DataTable.isDataTable(el)) {
                    new DataTable(el).destroy();
                }
                new DataTable(el, {
                    pageLength: 10,
                    order: [[0, 'desc']],
                    language: {
                        search: 'Cari Sesi Opname:',
                        lengthMenu: 'Tampilkan _MENU_ data',
                        info: 'Menampilkan _START_ - _END_ dari _TOTAL_ sesi stock opname'
                    }
                });
            }
            document.addEventListener('DOMContentLoaded', initTable);
            document.addEventListener('livewire:navigated', initTable);
            initTable();
        })();
    </script>
</x-filament-panels::page>
