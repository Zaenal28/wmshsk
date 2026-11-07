<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BarangKeluar extends Model
{
    use HasFactory;

    protected $table = 'barang_keluars';

    protected $fillable = [
        'no_surat',
        'tanggal_keluar',
        'pic',
        'tujuan',
        'nama_penerima',
        'total_jumlah',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_keluar' => 'date',
            'total_jumlah' => 'integer',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(BarangKeluarItem::class, 'barang_keluar_id');
    }

    /**
     * Daftar pilihan Tujuan default beserta Tujuan baru yang pernah ditambahkan
     */
    public static function getTujuanList(): array
    {
        $defaults = [
            'Apotek Cianjur',
            'Ruang Facial Cianjur',
            'Marketplace',
            'Gudang Purwakarta',
            'Gudang Bandung',
        ];

        $fromDb = static::query()
            ->whereNotNull('tujuan')
            ->where('tujuan', '!=', '')
            ->distinct()
            ->pluck('tujuan')
            ->toArray();

        $fromSession = session('custom_tujuan_barang_keluar', []);

        return array_values(array_unique(array_merge($defaults, $fromDb, $fromSession)));
    }
}
