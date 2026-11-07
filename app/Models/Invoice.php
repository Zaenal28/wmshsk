<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';

    protected $fillable = [
        'no_invoice',
        'tanggal_invoice',
        'tujuan',
        'nama_tempat',
        'alamat_tempat',
        'dari_tanggal',
        'ke_tanggal',
        'sub_total',
        'diskon_persen',
        'diskon_nominal',
        'total_tagihan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_invoice' => 'date',
            'dari_tanggal' => 'date',
            'ke_tanggal' => 'date',
            'sub_total' => 'decimal:2',
            'diskon_persen' => 'decimal:2',
            'diskon_nominal' => 'decimal:2',
            'total_tagihan' => 'decimal:2',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }

    public function getPeriodeTransaksiAttribute(): string
    {
        $dari = $this->dari_tanggal ? $this->dari_tanggal->format('d/m/Y') : '-';
        $ke = $this->ke_tanggal ? $this->ke_tanggal->format('d/m/Y') : '-';

        return "{$dari} s/d {$ke}";
    }

    /**
     * Daftar pilihan Nama Tempat / Alamat default beserta alamat baru yang pernah ditambahkan
     */
    public static function getAlamatList(): array
    {
        $defaults = [
            'Jl. Ir. H. Juanda No.85, Bojongherang, Kec. Cianjur, Kabupaten Cianjur, Jawa Barat 43216',
            'Jl. A. Yani No.888, Cicaheum, Kec. Kiaracondong, Kota Bandung, Jawa Barat 40282',
            'Jl. Veteran No.211, Ciseureuh, Kec. Purwakarta, Kabupaten Purwakarta, Jawa Barat 41118',
        ];

        $fromDb = static::query()
            ->whereNotNull('nama_tempat')
            ->where('nama_tempat', '!=', '')
            ->distinct()
            ->pluck('nama_tempat')
            ->toArray();

        return array_values(array_unique(array_merge($defaults, $fromDb)));
    }
}
