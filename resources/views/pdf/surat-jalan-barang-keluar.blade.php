<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan - {{ $data->no_surat }}</title>
    <style>
        @page {
            margin: 35px 45px;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #000000;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 1px;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            text-align: center;
        }
        .company-address {
            font-size: 10px;
            margin: 0;
            text-align: center;
        }
        .divider {
            border: 0;
            border-top: 1.5px solid #000000;
            margin: 0 0 18px 0;
        }
        .title-section {
            text-align: center;
            margin-bottom: 18px;
        }
        .doc-title {
            font-size: 15px;
            font-weight: bold;
            text-decoration: underline;
            margin: 0 0 4px 0;
            letter-spacing: 0.5px;
        }
        .doc-number {
            font-size: 11px;
            margin: 0;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 12px;
            border-collapse: collapse;
        }
        .meta-table td {
            font-size: 11px;
            padding: 2px 0;
            vertical-align: top;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 35px;
        }
        .items-table th {
            border: 1px solid #000000;
            padding: 6px 8px;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }
        .items-table td {
            border: 1px solid #000000;
            padding: 6px 8px;
            font-size: 13px;
            vertical-align: middle;
        }
        .text-center {
            text-align: center;
        }
        .signature-table {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
        }
        .signature-table td {
            width: 50%;
            text-align: center;
            font-size: 11px;
            vertical-align: top;
        }
        .signature-space {
            height: 60px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
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
    @endphp

    <table class="header-table">
        <tr>
            <td style="width: 18%;"></td>
            <td style="width: 64%; text-align: center;">
                <div class="company-name">PT HSK BIODERMA</div>
                <div class="company-address">Jl. Veteran No. 211 Ciseureuh - Purwakarta, Jawa Barat 41118</div>
            </td>
            <td style="width: 18%; text-align: right;">
                @if ($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo HSK" style="height: 52px; width: auto;" />
                @endif
            </td>
        </tr>
    </table>

    <hr class="divider">

    <div class="title-section">
        <div class="doc-title">SURAT JALAN</div>
        <div class="doc-number">No. {{ $data->no_surat }}</div>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 11%;">Tanggal</td>
            <td style="width: 39%;">: {{ $data->tanggal_keluar ? $data->tanggal_keluar->format('d/m/Y') : '-' }}</td>
            <td style="width: 11%;">Tujuan</td>
            <td style="width: 39%;">: {{ strtoupper($data->tujuan) }}</td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 6%;">NO</th>
                <th style="width: 44%;">NAMA BARANG</th>
                <th style="width: 12%;">QTY</th>
                <th style="width: 16%;">EXPIRED</th>
                <th style="width: 22%;">KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ strtoupper($item->nama_barang) }}</td>
                    <td class="text-center">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                    <td class="text-center">
                        {{ $item->tanggal_expired ? $item->tanggal_expired->format('n/Y') : '' }}
                    </td>
                    <td>{{ $item->keterangan ?? '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada daftar barang</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="signature-table">
        <tr>
            <td>
                <div>Dikeluarkan Oleh,</div>
                <div class="signature-space"></div>
                <div>( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class="signature-name">{{ strtoupper($data->pic ?: 'ADMIN') }}</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</div>
                <div>Admin Gudang Pusat</div>
            </td>
            <td>
                <div>Diterima Oleh,</div>
                <div class="signature-space"></div>
                <div>( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class="signature-name">{{ strtoupper($data->nama_penerima ?: '................') }}</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</div>
                <div>Admin Cabang / Penerima</div>
            </td>
        </tr>
    </table>
</body>
</html>
