<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RacikanBahan extends Model
{
    use HasFactory;

    protected $table = 'racikan_bahans';

    protected $fillable = [
        'racikan_id',
        'bahan_barang_id',
        'no_bahan',
        'nama_bahan',
        'jumlah_pakai',
        'satuan',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_pakai' => 'integer',
        ];
    }

    public function racikan(): BelongsTo
    {
        return $this->belongsTo(Racikan::class, 'racikan_id');
    }

    public function bahanBarang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'bahan_barang_id');
    }
}
