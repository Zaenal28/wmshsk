<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Racikan extends Model
{
    use HasFactory;

    protected $table = 'racikans';

    protected $fillable = [
        'kode_racik',
        'tanggal_racik',
        'barang_asal_id',
        'barang_hasil_id',
        'nama_barang',
        'jumlah_racik',
        'tanggal_expired_hasil',
        'pic',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_racik' => 'date',
            'tanggal_expired_hasil' => 'date',
            'jumlah_racik' => 'integer',
        ];
    }

    public function barangAsal(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_asal_id');
    }

    public function barangHasil(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_hasil_id');
    }

    public function bahans(): HasMany
    {
        return $this->hasMany(RacikanBahan::class, 'racikan_id');
    }
}
