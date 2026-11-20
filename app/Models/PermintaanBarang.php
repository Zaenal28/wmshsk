<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class PermintaanBarang extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_surat',
        'tanggal_permintaan',
        'cabang',
        'pic',
        'jumlah_barang',
        'status',
        'catatan',
        'user_id',
    ];

    protected $casts = [
        'tanggal_permintaan' => 'date',
        'jumlah_barang' => 'integer',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(PermintaanBarangItem::class, 'permintaan_barang_id');
    }

    /**
     * Generate format No Surat: HSK/001/tanggal-bulan-tahun
     * Contoh: HSK/001/11-11-2026
     */
    public static function generateNoSurat(?string $tanggal = null): string
    {
        $date = $tanggal ? Carbon::parse($tanggal) : now();
        $dateFormatted = $date->format('d-m-Y'); // tanggal-bulan-tahun

        // Hitung total data permintaan untuk nomor urut
        $nextNumber = static::count() + 1;
        $padded = str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);

        return "HSK/{$padded}/{$dateFormatted}";
    }

    /**
     * Daftar preset cabang pilihan:
     * Ruang Facial, Apotek Cianjur, Marketplace, Gudang Bandung, Gudang PWK
     */
    public static function getCabangList(): array
    {
        return [
            'Ruang Facial',
            'Apotek Cianjur',
            'Marketplace',
            'Gudang Bandung',
            'Gudang PWK',
        ];
    }
}
