<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarangKeluarItem extends Model
{
    use HasFactory;

    protected $table = 'barang_keluar_items';

    protected $fillable = [
        'barang_keluar_id',
        'barang_id',
        'no_barang',
        'nama_barang',
        'tanggal_expired',
        'jumlah',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_expired' => 'date',
            'jumlah' => 'integer',
        ];
    }

    public function barangKeluar(): BelongsTo
    {
        return $this->belongsTo(BarangKeluar::class, 'barang_keluar_id');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
