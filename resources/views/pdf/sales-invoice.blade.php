<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sales Invoice - {{ $invoice->no_invoice }}</title>
    <style>
        @page {
            size: 21cm 29.7cm;
            margin-top: 3cm;
            margin-right: 3cm;
            margin-bottom: 3cm;
            margin-left: 3cm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000000;
            margin: 0;
            padding: 0;
        }
        .top-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        .top-grid td {
            vertical-align: top;
        }
        .box {
            border: 0.8px solid #000000;
            padding: 8px 10px;
        }
        .dashed-bottom {
            border-bottom: 1px dashed #000000;
            padding-bottom: 6px;
            margin-bottom: 6px;
        }
        .company-title {
            font-size: 13px;
            font-weight: normal;
            text-transform: uppercase;
        }
        .company-addr {
            font-size: 11px;
            line-height: 1.35;
            text-transform: uppercase;
        }
        .sales-invoice-title {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            padding: 18px 8px;
        }
        .meta-box-title {
            font-size: 11px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .meta-box-val {
            font-size: 11px;
            text-align: center;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .items-table th {
            border: 0.8px solid #000000;
            padding: 6px 8px;
            font-size: 10.5px;
            font-weight: bold;
            text-align: center;
        }
        .items-table td {
            border: 0.8px solid #000000;
            padding: 4px 8px;
            font-size: 10.5px;
            vertical-align: middle;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .summary-table td {
            padding: 4px 8px;
            font-size: 12px;
        }
        .summary-label {
            border: 0.8px solid #000000;
            font-weight: normal;
            font-size: 13px;
        }
        .summary-val {
            border: 0.8px solid #000000;
            text-align: right;
            font-size: 11px;
        }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('images/logo-hsk.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }
        $bulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $tglInv = $invoice->tanggal_invoice
            ? $invoice->tanggal_invoice->format('j') . ' ' . ($bulanIndo[(int) $invoice->tanggal_invoice->format('n')] ?? '') . ' ' . $invoice->tanggal_invoice->format('Y')
            : '-';
    @endphp

    <table class="top-grid">
        <tr>
            {{-- Kolom Kiri: Kop PT HSK BIODERMA & Bill To --}}
            <td style="width: 52%;">
                <div class="box" style="margin-bottom: 12px;">
                    <div class="company-title dashed-bottom">PT HSK BIODERMA</div>
                    <div class="company-addr">
                        JL. VETERAN NO. 211,RT.004.RW001<br>
                        CISEUREUH, PURWAKARTA
                    </div>
                </div>

                <div class="box">
                    <div class="company-title dashed-bottom">
                        Bil To : {{ strtoupper($invoice->tujuan) }}
                    </div>
                    <div style="font-size: 11px; line-height: 1.4; min-height: 32px;">
                        {{ $invoice->nama_tempat ?: $invoice->tujuan }}
                        @if (!empty($invoice->alamat_tempat))
                            <br>{{ $invoice->alamat_tempat }}
                        @endif
                    </div>
                </div>
            </td>

            {{-- Spacer Tengah --}}
            <td style="width: 6%;"></td>

            {{-- Kolom Kanan: Sales Invoice + Logo + Invoice Date & Invoice No --}}
            <td style="width: 42%;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td colspan="2" class="box sales-invoice-title">
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="text-align: center; vertical-align: middle; font-size: 20px; font-weight: bold;">
                                        Sales Invoice
                                    </td>
                                    @if ($logoBase64)
                                        <td style="width: 55px; text-align: right; vertical-align: middle;">
                                            <img src="{{ $logoBase64 }}" alt="Logo HSK" style="height: 40px; width: auto;" />
                                        </td>
                                    @endif
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td class="box" style="width: 50%; height: 68px; vertical-align: middle;">
                            <div class="meta-box-title">INVOICE DATE</div>
                            <div class="meta-box-val">{{ $tglInv }}</div>
                        </td>
                        <td class="box" style="width: 50%; height: 68px; vertical-align: middle;">
                            <div class="meta-box-title">INVOICE NO</div>
                            <div class="meta-box-val">{{ $invoice->no_invoice }}</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- TABEL ITEM BARANG INVOICE --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 6%;">no.</th>
                <th style="width: 40%;">Item description</th>
                <th style="width: 12%;">QTY</th>
                <th style="width: 12%;">Item Unit</th>
                <th style="width: 15%;">unit price</th>
                <th style="width: 15%;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($invoice->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->item_description }}</td>
                    <td class="text-center">{{ number_format($item->qty, 0, ',', ',') }}</td>
                    <td class="text-center">{{ $item->item_unit ?: 'pcs' }}</td>
                    <td class="text-right">Rp{{ number_format((float) $item->unit_price, 0, ',', ',') }}</td>
                    <td class="text-right">Rp{{ number_format((float) $item->amount, 0, ',', ',') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data barang pada invoice ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- FOOTER REKAP SUB TOTAL, DISKON 20%, TOTAL --}}
    <table class="summary-table">
        <tr>
            <td style="width: 58%;"></td>
            <td class="summary-label" style="width: 27%;">Sub Total</td>
            <td class="summary-val" style="width: 15%;">Rp{{ number_format((float) $invoice->sub_total, 0, ',', ',') }}</td>
        </tr>
        <tr>
            <td></td>
            <td class="summary-label">Discount {{ (float) $invoice->diskon_persen == 20 ? '20%' : rtrim(rtrim(number_format((float) $invoice->diskon_persen, 2), '0'), '.') . '%' }}</td>
            <td class="summary-val">Rp{{ number_format((float) $invoice->diskon_nominal, 0, ',', ',') }}</td>
        </tr>
        <tr>
            <td></td>
            <td class="summary-label" style="font-weight: bold;">Total</td>
            <td class="summary-val" style="font-weight: bold;">Rp{{ number_format((float) $invoice->total_tagihan, 0, ',', ',') }}</td>
        </tr>
    </table>
</body>
</html>
