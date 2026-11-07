<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:x="urn:schemas-microsoft-com:office:excel"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body, table, td, th {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            color: #000000;
        }
        table {
            border-collapse: collapse;
        }
        .border-box {
            border: 1pt solid #000000;
        }
        .border-top {
            border-top: 1pt solid #000000;
        }
        .border-bottom {
            border-bottom: 1pt solid #000000;
        }
        .border-left {
            border-left: 1pt solid #000000;
        }
        .border-right {
            border-right: 1pt solid #000000;
        }
        .border-dashed-bottom {
            border-bottom: 1pt dashed #000000;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .font-bold {
            font-weight: bold;
        }
        .title-sales {
            font-size: 16pt;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    @php
        $bulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $tglInv = $invoice->tanggal_invoice
            ? $invoice->tanggal_invoice->format('j') . ' ' . ($bulanIndo[(int) $invoice->tanggal_invoice->format('n')] ?? '') . ' ' . $invoice->tanggal_invoice->format('Y')
            : '-';
    @endphp

    <table>
        <col width="45" />
        <col width="260" />
        <col width="65" />
        <col width="75" />
        <col width="115" />
        <col width="125" />

        {{-- BARIS KOP ATAS --}}
        <tr>
            <td colspan="3" class="border-top border-left border-right border-dashed-bottom" style="padding: 6px; font-size: 11pt;">
                PT HSK BIODERMA
            </td>
            <td></td>
            <td colspan="2" rowspan="2" class="border-box title-sales">
                Sales Invoice
            </td>
        </tr>
        <tr>
            <td colspan="3" class="border-left border-right border-bottom" style="padding: 6px;">
                JL. VETERAN NO. 211,RT.004.RW001<br />
                CISEUREUH, PURWAKARTA
            </td>
            <td></td>
        </tr>

        <tr>
            <td colspan="3" style="height: 10px;"></td>
            <td></td>
            <td class="border-left border-right" style="height: 10px;"></td>
            <td class="border-left border-right" style="height: 10px;"></td>
        </tr>

        {{-- BARIS BILL TO & INVOICE DATE / NO --}}
        <tr>
            <td colspan="3" class="border-top border-left border-right border-dashed-bottom" style="padding: 6px; font-size: 11pt;">
                Bil To : {{ strtoupper($invoice->tujuan) }}
            </td>
            <td></td>
            <td class="border-left border-right text-center font-bold" style="vertical-align: bottom;">
                INVOICE DATE
            </td>
            <td class="border-left border-right text-center font-bold" style="vertical-align: bottom;">
                INVOICE NO
            </td>
        </tr>
        <tr>
            <td colspan="3" class="border-left border-right border-bottom" style="padding: 6px;">
                {{ $invoice->nama_tempat ?: $invoice->tujuan }}
            </td>
            <td></td>
            <td class="border-left border-right border-bottom text-center" style="vertical-align: top;">
                {{ $tglInv }}
            </td>
            <td class="border-left border-right border-bottom text-center" style="vertical-align: top;">
                {{ $invoice->no_invoice }}
            </td>
        </tr>

        <tr>
            <td colspan="6" style="height: 18px;"></td>
        </tr>

        {{-- HEADER TABEL BARANG --}}
        <tr>
            <th class="border-box text-center font-bold" style="padding: 6px;">no.</th>
            <th class="border-box text-center font-bold" style="padding: 6px;">Item description</th>
            <th class="border-box text-center font-bold" style="padding: 6px;">QTY</th>
            <th class="border-box text-center font-bold" style="padding: 6px;">Item Unit</th>
            <th class="border-box text-center font-bold" style="padding: 6px;">unit price</th>
            <th class="border-box text-center font-bold" style="padding: 6px;">Amount</th>
        </tr>

        {{-- ISI ITEM BARANG --}}
        @foreach ($invoice->items as $idx => $item)
            <tr>
                <td class="border-box text-center" style="padding: 4px;">{{ $idx + 1 }}</td>
                <td class="border-box" style="padding: 4px;">{{ $item->item_description }}</td>
                <td class="border-box text-center" style="padding: 4px;">{{ (int) $item->qty }}</td>
                <td class="border-box text-center" style="padding: 4px;">{{ $item->item_unit ?: 'pcs' }}</td>
                <td class="border-box text-right" style="padding: 4px; mso-number-format:'\R\p\#\,\#\#0';">
                    Rp{{ number_format((float) $item->unit_price, 0, ',', ',') }}
                </td>
                <td class="border-box text-right" style="padding: 4px; mso-number-format:'\R\p\#\,\#\#0';">
                    Rp{{ number_format((float) $item->amount, 0, ',', ',') }}
                </td>
            </tr>
        @endforeach

        <tr>
            <td colspan="6" style="height: 12px;"></td>
        </tr>

        {{-- FOOTER REKAP SUB TOTAL, DISKON 20%, TOTAL --}}
        <tr>
            <td colspan="3"></td>
            <td colspan="2" class="border-box" style="padding: 5px; font-size: 11pt;">Sub Total</td>
            <td class="border-box text-right" style="padding: 5px; mso-number-format:'\R\p\#\,\#\#0';">
                Rp{{ number_format((float) $invoice->sub_total, 0, ',', ',') }}
            </td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td colspan="2" class="border-box" style="padding: 5px; font-size: 11pt;">
                Discount {{ (float) $invoice->diskon_persen == 20 ? '20%' : rtrim(rtrim(number_format((float) $invoice->diskon_persen, 2), '0'), '.') . '%' }}
            </td>
            <td class="border-box text-right" style="padding: 5px; mso-number-format:'\R\p\#\,\#\#0';">
                Rp{{ number_format((float) $invoice->diskon_nominal, 0, ',', ',') }}
            </td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td colspan="2" class="border-box font-bold" style="padding: 5px; font-size: 11pt;">Total</td>
            <td class="border-box text-right font-bold" style="padding: 5px; mso-number-format:'\R\p\#\,\#\#0';">
                Rp{{ number_format((float) $invoice->total_tagihan, 0, ',', ',') }}
            </td>
        </tr>
    </table>
</body>
</html>
