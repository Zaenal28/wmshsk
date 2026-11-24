<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            font-family: Calibri, Arial, sans-serif;
            font-size: 11pt;
        }
        th, td {
            border: 1px solid #000000;
            padding: 5px;
            text-align: center;
            vertical-align: middle;
        }
        .header-title {
            font-size: 16pt;
            font-weight: bold;
            text-align: center;
            border: none;
            padding: 15px;
        }
        .bg-pink { background-color: #FCE7F3; }
        .bg-gray { background-color: #E2E8F0; }
        .bg-ap { background-color: #DBEAFE; }
        .bg-rf { background-color: #FCE7F3; }
        .bg-mp { background-color: #FFE4E6; }
        .bg-pwk { background-color: #FFEDD5; }
        .bg-bdg { background-color: #DCFCE7; }
        .bg-racik { background-color: #FEF9C3; }
        .bg-kategori { background-color: #F1F5F9; font-weight: bold; font-size: 12pt; text-align: center; }
        .bg-pesan { background-color: #FEF08A; font-weight: bold; color: #854D0E; }
        .bg-discontinue { background-color: #E2E8F0; color: #475569; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="23" class="header-title">STOK GUDANG PUSAT {{ strtoupper($periodeLabel) }}</td>
        </tr>
        <thead>
            <tr>
                <th rowspan="2" class="bg-pink">no.</th>
                <th rowspan="2" class="bg-pink">Nama Produk</th>
                <th rowspan="2" class="bg-gray">Rata-rata<br>3 bulan</th>
                <th rowspan="2" class="bg-gray">Rata rata<br>2 bulan</th>
                <th rowspan="2" class="bg-gray">Rata-rata<br>1 bulan</th>
                <th rowspan="2" class="bg-gray">Stok<br>awal</th>
                <th colspan="2" class="bg-gray">Barang Masuk</th>
                <th colspan="6" class="bg-gray">PENGELUARAN</th>
                <th rowspan="2" class="bg-gray">Total<br>keluar</th>
                <th rowspan="2" class="bg-pink">retur<br>pabrik</th>
                <th rowspan="2" class="bg-gray">Stok<br>akhir</th>
                <th rowspan="2" class="bg-gray">Kekurangan (-)<br>Kelebihan (+)<br>3 bulan</th>
                <th rowspan="2" class="bg-gray">Kekurangan (-)<br>Kelebihan (+)<br>2 bulan</th>
                <th rowspan="2" class="bg-pink">Pesan</th>
                <th rowspan="2" class="bg-pink">TANGGAL<br>PESAN</th>
                <th rowspan="2" class="bg-pink">STOK<br>PABRIK</th>
                <th rowspan="2" class="bg-pink">expired 1</th>
                <th rowspan="2" class="bg-pink">expired 2</th>
            </tr>
            <tr>
                <th class="bg-gray">Pabrik</th>
                <th class="bg-gray">Cabang</th>
                <th class="bg-ap">AP</th>
                <th class="bg-rf">RF</th>
                <th class="bg-mp">MP</th>
                <th class="bg-pwk">PWK</th>
                <th class="bg-bdg">BDG</th>
                <th class="bg-racik">RACIK</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($groups as $kategori => $items)
                <tr>
                    <td colspan="23" class="bg-kategori">{{ $kategori }}</td>
                </tr>
                @foreach ($items as $item)
                    @php
                        $pesanLower = strtolower($item->status_pesan);
                        $classPesan = $pesanLower === 'pesan' ? 'bg-pesan' : ($pesanLower === 'discontinue' ? 'bg-discontinue' : '');
                    @endphp
                    <tr>
                        <td>{{ $item->no_urut }}</td>
                        <td class="text-left font-bold">{{ $item->nama_barang }}</td>
                        <td>{{ $item->rata_3_bulan ?: '' }}</td>
                        <td>{{ $item->rata_2_bulan ?: '' }}</td>
                        <td>{{ $item->rata_1_bulan ?: '' }}</td>
                        <td>{{ $item->stok_awal ?: 0 }}</td>
                        <td>{{ $item->masuk_pabrik ?: '' }}</td>
                        <td>{{ $item->masuk_cabang ?: '' }}</td>
                        <td>{{ $item->keluar_ap ?: '' }}</td>
                        <td>{{ $item->keluar_rf ?: '' }}</td>
                        <td>{{ $item->keluar_mp ?: '' }}</td>
                        <td>{{ $item->keluar_pwk ?: '' }}</td>
                        <td>{{ $item->keluar_bdg ?: '' }}</td>
                        <td>{{ $item->keluar_racik ?: '' }}</td>
                        <td class="font-bold">{{ $item->total_keluar ?: 0 }}</td>
                        <td>{{ $item->retur_pabrik ?: '' }}</td>
                        <td class="font-bold">{{ $item->stok_akhir ?: 0 }}</td>
                        <td style="{{ $item->selisih_3_bulan < 0 ? 'color: red;' : '' }}">{{ $item->selisih_3_bulan }}</td>
                        <td style="{{ $item->selisih_2_bulan < 0 ? 'color: red;' : '' }}">{{ $item->selisih_2_bulan }}</td>
                        <td class="{{ $classPesan }}">{{ $item->status_pesan }}</td>
                        <td>{{ $item->tanggal_pesan ? $item->tanggal_pesan->format('d/m') : '' }}</td>
                        <td>{{ $item->stok_pabrik ?: '' }}</td>
                        <td>{{ $item->expired_1 ?: '' }}</td>
                        <td>{{ $item->expired_2 ?: '' }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>
