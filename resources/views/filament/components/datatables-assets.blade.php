@once
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
    <style>
        /* Perlebar container utama halaman Filament agar card tabel penuh, pas, dan rapi */
        .fi-main {
            max-width: 100% !important;
            padding-left: 1.5rem !important;
            padding-right: 1.5rem !important;
        }
        @media (min-width: 1024px) {
            .fi-main {
                padding-left: 2rem !important;
                padding-right: 2rem !important;
            }
        }

        /* Kustomisasi DataTables & Tipografi Tailwind agar seluruh teks dan kolom pas tanpa scrollbar tidak perlu */
        .dt-container {
            font-family: inherit;
            font-size: 0.875rem;
            line-height: 1.5;
            color: inherit;
            width: 100% !important;
        }
        .dt-container .dt-layout-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 1rem 1.5rem;
            margin: 0 !important;
        }
        .dt-container .dt-layout-row.dt-layout-table {
            padding: 0 !important;
            display: block;
            width: 100% !important;
        }
        .dt-container .dt-layout-cell {
            width: 100%;
        }
        .dt-container .dt-length label,
        .dt-container .dt-search label {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #4b5563;
        }
        .dark .dt-container .dt-length label,
        .dark .dt-container .dt-search label {
            color: #d1d5db;
        }
        .dt-container .dt-search input,
        .dt-container .dt-length select {
            border-radius: 0.5rem !important;
            border-color: #d1d5db !important;
            border-width: 1px !important;
            border-style: solid !important;
            padding: 0.45rem 0.85rem !important;
            font-size: 0.875rem !important;
            background-color: #ffffff !important;
            color: #111827 !important;
            outline: none !important;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .dt-container .dt-search input {
            min-width: 240px;
        }
        .dt-container .dt-search input:focus,
        .dt-container .dt-length select:focus {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
        }
        .dark .dt-container .dt-search input,
        .dark .dt-container .dt-length select {
            border-color: #374151 !important;
            color: #f3f4f6 !important;
            background-color: #1f2937 !important;
        }
        table.dataTable {
            width: 100% !important;
            border-collapse: collapse !important;
            margin: 0 !important;
            table-layout: auto !important;
        }
        table.dataTable thead th,
        table.dataTable thead td {
            border-top: 1px solid #e5e7eb !important;
            border-bottom: 1px solid #e5e7eb !important;
            background-color: #f8fafc !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            font-size: 0.75rem !important;
            letter-spacing: 0.04em;
            color: #475569 !important;
            padding: 1rem 1.25rem !important;
            white-space: nowrap;
            vertical-align: middle !important;
        }
        .dark table.dataTable thead th,
        .dark table.dataTable thead td {
            border-top: 1px solid #374151 !important;
            border-bottom: 1px solid #374151 !important;
            background-color: #111827 !important;
            color: #9ca3af !important;
        }
        table.dataTable tbody tr {
            background-color: transparent !important;
            transition: background-color 0.12s ease;
        }
        table.dataTable tbody tr:hover {
            background-color: #f1f5f9 !important;
        }
        .dark table.dataTable tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.03) !important;
        }
        table.dataTable tbody td {
            padding: 1.05rem 1.25rem !important;
            border-bottom: 1px solid #f1f5f9 !important;
            font-size: 0.875rem !important;
            color: #1e293b;
            vertical-align: middle !important;
        }
        .dark table.dataTable tbody td {
            border-bottom: 1px solid #1f2937 !important;
            color: #e2e8f0;
        }
        .dt-container .dt-info {
            font-size: 0.8125rem;
            font-weight: 500;
            color: #64748b;
            padding-top: 0.25rem;
        }
        .dt-container .dt-paging {
            padding-top: 0.25rem;
        }
        .dt-container .dt-paging .dt-paging-button {
            border-radius: 0.5rem !important;
            font-size: 0.8125rem !important;
            font-weight: 600 !important;
            padding: 0.4rem 0.85rem !important;
            margin: 0 0.125rem !important;
        }
        .dt-container .dt-paging .dt-paging-button.current,
        .dt-container .dt-paging .dt-paging-button.current:hover {
            background: #2563eb !important;
            color: #ffffff !important;
            border: 1px solid #2563eb !important;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
@endonce
