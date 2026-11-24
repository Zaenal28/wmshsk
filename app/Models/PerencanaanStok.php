<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerencanaanStok extends Model
{
    use HasFactory;

    protected $table = 'perencanaan_stoks';

    protected $fillable = [
        'periode',
        'tipe_periode',
        'tanggal_mulai',
        'tanggal_selesai',
        'barang_id',
        'no_urut',
        'nama_barang',
        'kategori',
        'rata_3_bulan',
        'rata_2_bulan',
        'rata_1_bulan',
        'stok_awal',
        'masuk_pabrik',
        'masuk_cabang',
        'keluar_ap',
        'keluar_rf',
        'keluar_mp',
        'keluar_pwk',
        'keluar_bdg',
        'keluar_racik',
        'total_keluar',
        'retur_pabrik',
        'stok_akhir',
        'status_pesan',
        'is_manual_pesan',
        'tanggal_pesan',
        'stok_pabrik',
        'expired_1',
        'expired_2',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'tanggal_pesan' => 'date',
            'rata_3_bulan' => 'integer',
            'rata_2_bulan' => 'integer',
            'rata_1_bulan' => 'integer',
            'stok_awal' => 'integer',
            'masuk_pabrik' => 'integer',
            'masuk_cabang' => 'integer',
            'keluar_ap' => 'integer',
            'keluar_rf' => 'integer',
            'keluar_mp' => 'integer',
            'keluar_pwk' => 'integer',
            'keluar_bdg' => 'integer',
            'keluar_racik' => 'integer',
            'total_keluar' => 'integer',
            'retur_pabrik' => 'integer',
            'stok_akhir' => 'integer',
            'stok_pabrik' => 'integer',
            'is_manual_pesan' => 'boolean',
        ];
    }

    public static function standardizeProductName(string $name): string
    {
        $name = trim($name);
        $nameMap = [
            'Cleasing Oil' => 'HSK Secret Cleasing Oil',
            'Eye Make Up Remover' => 'HSK Secret Eye Make Up Remover',
            'Micellar Milk' => 'HSK Secret Micellar Milk',
            'Micellar Water' => 'HSK Secret Micellar Water',
            'Milky Micellar' => 'HSK Secret Milky Micellar',
            'Fw. Dry & sensitive' => 'HSK Secret Fw. Dry & sensitive',
            'Fw. Oily' => 'HSK Secret Fw. Oily',
            'Fw. Foam' => 'HSK Secret Fw. Foam',
            'AA Sunscreen' => 'HSK Secret AA Sunscreen',
            'Acne Sun Protector' => 'HSK Secret Acne Sun Protector',
            'Flawless Skin' => 'HSK Secret Flawless Skin',
            'Glow Sunscreen' => 'HSK Secret Glow Sunscreen',
            'SS Sunscreen' => 'HSK Secret SS Sunscreen',
            'Sunshield' => 'HSK Secret Sunshield',
            'WL Sunscreen' => 'HSK Secret WL Sunscreen',
            'WN Sunscreen' => 'HSK Secret WN Sunscreen',
            'AC-2' => 'HSK Secret AC-2',
            'ASCSB' => 'HSK Secret Acne Skin Barier',
            'ACSB' => 'HSK Secret Acne Skin Barier',
            'AH-1' => 'HSK Secret AH-1',
            'Essential Moist' => 'HSK Secret Essential Moist',
            'Glowing Gel' => 'HSK Secret Glowing Gel',
            'Intensive Moist' => 'HSK Secret Intensive Moist',
            'WP Moist' => 'HSK Secret WP Moist',
            'AA Skin Booster' => 'HSK Secret Anti-Aging Skin Booster',
            'AC-1' => 'HSK Secret AC-1',
            'AC-3' => 'HSK Secret AC-3',
            'LUMINOUS GOLD' => 'HSK Secret LUMINOUS GOLD',
            'MS 2' => 'HSK Secret MS 2',
            'Radiant White' => 'HSK Secret Radiant White',
            'SS Serum' => 'HSK Secret SS Serum',
            'Wm Serum' => 'HSK Secret Wm Serum',
            'Acne Night Cream' => 'HSK Secret Acne Night Cream',
            'Acne Reducer' => 'HSK Secret Acne Reducer',
            'Brightening Complex' => 'HSK Secret Brightening Complex',
            'LG-1 Night Cream' => 'HSK Secret LG-1 Night Cream',
            'LG-2 Night Cream' => 'HSK Secret LG-2 Night Cream',
            'LG-3 Night Cream' => 'HSK Secret LG-3 Night Cream',
            'P-0 Night Cream' => 'HSK Secret P-0 Night Cream',
            'P-1 Night Cream' => 'HSK Secret P-1 Night Cream',
            'P-2 Night Cream' => 'HSK Secret P-2 Night Cream',
            'Shine & Bright' => 'HSK Secret Shine & Bright',
            'Brightening Toner' => 'HSK Secret Brightening Toner',
            'Toner For Oily Skin' => 'HSK Secret Toner For Oily Skin',
            'Masker Acne' => 'HSK Secret Masker Acne',
            'Masker Acne Oles' => 'HSK Secret Masker Acne',
            'Masker Vit C' => 'Hsk Secret Masker Vit C',
            'Hand Body Lotion' => 'HSK Secret Hand Body Lotion',
            'Ac Expert' => 'HSK Secret Ac Expert',
        ];

        return $nameMap[$name] ?? $name;
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    /**
     * Hitung kekurangan (-) / kelebihan (+) 3 bulan = Stok Akhir - Rata 3 bulan
     */
    public function getSelisih3BulanAttribute(): int
    {
        return (int) $this->stok_akhir - (int) $this->rata_3_bulan;
    }

    /**
     * Hitung kekurangan (-) / kelebihan (+) 2 bulan = Stok Akhir - Rata 2 bulan
     */
    public function getSelisih2BulanAttribute(): int
    {
        return (int) $this->stok_akhir - (int) $this->rata_2_bulan;
    }

    /**
     * Hitung status pesan otomatis sesuai rumus spreadsheet:
     * =IF(stok_akhir < rata_1_bulan; "Pesan"; "Tidak")
     * Jika manual atau discontinue, gunakan pilihan pengguna.
     */
    public function getStatusPesanOtomatisAttribute(): string
    {
        $status = trim((string) $this->status_pesan);

        if (strtolower($status) === 'discontinue') {
            return 'Discontinue';
        }

        if ($this->is_manual_pesan) {
            return in_array($status, ['Pesan', 'Tidak', 'Discontinue']) ? $status : 'Tidak';
        }

        // Rumus spreadsheet: =IF(stok_akhir < rata_1_bulan; "Pesan"; "Tidak")
        // "Jika stok akhir lebih kecil daripada rata-rata barang keluar 1 bulan, maka tampilkan tulisan Pesan. Jika tidak (stok masih sama atau lebih banyak), tampilkan tulisan Tidak."
        return ((int) $this->stok_akhir < (int) $this->rata_1_bulan) ? 'Pesan' : 'Tidak';
    }

    /**
     * Badge visual status pesan sesuai logika spreadsheet
     */
    public function getBadgeStatusPesanAttribute(): array
    {
        $status = strtolower(trim((string) $this->status_pesan_otomatis));

        if ($status === 'discontinue') {
            return [
                'label' => 'discontinue',
                'bg' => '#f1f5f9',
                'text' => '#475569',
                'border' => '#cbd5e1',
            ];
        }

        if ($status === 'pesan') {
            return [
                'label' => 'Pesan',
                'bg' => '#fef08a',
                'text' => '#854d0e',
                'border' => '#facc15',
            ];
        }

        return [
            'label' => 'Tidak',
            'bg' => '#f8fafc',
            'text' => '#334155',
            'border' => '#e2e8f0',
        ];
    }
}
