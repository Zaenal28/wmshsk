<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermintaanBarangItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'permintaan_barang_id',
        'barang_id',
        'nama_barang',
        'jumlah',
        'satuan',
        'catatan',
    ];

    protected $casts = [
        'jumlah' => 'integer',
    ];

    public function permintaanBarang(): BelongsTo
    {
        return $this->belongsTo(PermintaanBarang::class, 'permintaan_barang_id');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
