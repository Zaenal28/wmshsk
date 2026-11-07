<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barangs';

    protected $fillable = [
        'no',
        'supplier',
        'nama_barang',
        'jumlah',
        'tanggal_masuk',
        'tanggal_expired',
        'kategori',
        'alokasi_penyimpanan',
        'satuan',
        'harga_barang',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'tanggal_masuk' => 'date',
            'tanggal_expired' => 'date',
            'harga_barang' => 'decimal:2',
        ];
    }

    public function barangKeluarItems(): HasMany
    {
        return $this->hasMany(BarangKeluarItem::class, 'barang_id');
    }

    /**
     * Daftar kategori default beserta kategori baru yang pernah diketik/ditambahkan ke database
     */
    public static function getKategoriOptions(): array
    {
        $defaults = [
            'Pembersih' => 'Pembersih',
            'Sabun' => 'Sabun',
            'Sunblock' => 'Sunblock',
            'Pelembab' => 'Pelembab',
            'Serum' => 'Serum',
            'Cream Malam' => 'Cream Malam',
            'Toner' => 'Toner',
            'Masker' => 'Masker',
            'Bedak' => 'Bedak',
            'Lainnya' => 'Lainnya',
            'Obat' => 'Obat',
            'VIT' => 'VIT',
            'Treatment Equiement' => 'Treatment Equiement',
            'KOREAN LUX' => 'KOREAN LUX',
            'Kebutuhan Treatment' => 'Kebutuhan Treatment',
            'Bahan Racik' => 'Bahan Racik',
        ];

        $customFromDb = static::query()
            ->whereNotNull('kategori')
            ->where('kategori', '!=', '')
            ->distinct()
            ->pluck('kategori', 'kategori')
            ->toArray();

        $customInSession = session('custom_kategori_barang', []);

        return array_merge($defaults, $customFromDb, $customInSession);
    }

    /**
     * Daftar Alokasi Penyimpanan default beserta lokasi baru yang pernah ditambahkan
     */
    public static function getAlokasiPenyimpananOptions(): array
    {
        $defaults = [
            'Gudang Pusat Lt1' => 'Gudang Pusat Lt1',
            'Gudang Lt 3' => 'Gudang Lt 3',
            'Gudang Kalimaya' => 'Gudang Kalimaya',
        ];

        $customFromDb = static::query()
            ->whereNotNull('alokasi_penyimpanan')
            ->where('alokasi_penyimpanan', '!=', '')
            ->distinct()
            ->pluck('alokasi_penyimpanan', 'alokasi_penyimpanan')
            ->toArray();

        $customInSession = session('custom_alokasi_penyimpanan', []);

        return array_merge($defaults, $customFromDb, $customInSession);
    }

    /**
     * Hitung masa berlaku (selisih dari Tanggal Masuk atau Hari Ini ke Tanggal Expired)
     * Format contoh: "1 tahun, 1 bulan, 7 hari"
     */
    public function getMasaBerlakuAttribute(): string
    {
        if (! $this->tanggal_expired || ! $this->tanggal_masuk) {
            return '-';
        }

        $start = Carbon::parse($this->tanggal_masuk)->startOfDay();
        $end = Carbon::parse($this->tanggal_expired)->startOfDay();

        if ($end->lessThanOrEqualTo($start)) {
            return '0 hari (Expired)';
        }

        $diff = $start->diff($end);

        $parts = [];
        if ($diff->y > 0) {
            $parts[] = $diff->y . ' tahun';
        }
        if ($diff->m > 0) {
            $parts[] = $diff->m . ' bulan';
        }
        if ($diff->d > 0 || empty($parts)) {
            $parts[] = $diff->d . ' hari';
        }

        return implode(', ', $parts);
    }

    /**
     * Ketentuan Status Expired:
     * - Safe: lebih dari 2/3 tahun (>= 24 bulan)
     * - Warning: menuju 1 tahun lagi mendekati expired (12 bulan s/d < 24 bulan)
     * - Danger: menuju beberapa bulan lagi (< 12 bulan dan belum melewati tanggal expired)
     * - Expired: sudah melebihi tanggal expired
     */
    public function getStatusExpiredInfoAttribute(): array
    {
        if (! $this->tanggal_expired || ! $this->tanggal_masuk) {
            return [
                'label' => '-',
                'bg' => '#f3f4f6',
                'text' => '#374151',
                'border' => '#d1d5db',
            ];
        }

        $acuan = Carbon::parse($this->tanggal_masuk)->startOfDay();
        $today = Carbon::now()->startOfDay();
        // Gunakan tanggal acuan (jika hari ini sudah melewati tanggal masuk, bisa juga cek expired terhadap tanggal masuk/hari ini)
        $baseDate = $today->greaterThan($acuan) ? $today : $acuan;
        $expired = Carbon::parse($this->tanggal_expired)->startOfDay();

        if ($expired->lessThanOrEqualTo($baseDate)) {
            return [
                'label' => 'Expired',
                'bg' => '#7f1d1d',     // Dark Red / Maroon
                'text' => '#ffffff',
                'border' => '#991b1b',
            ];
        }

        $totalMonths = ($baseDate->diff($expired)->y * 12) + $baseDate->diff($expired)->m;

        if ($totalMonths >= 24) {
            return [
                'label' => 'Safe',
                'bg' => '#dcfce7',     // Hijau (Safe)
                'text' => '#15803d',
                'border' => '#86efac',
            ];
        }

        if ($totalMonths >= 12) {
            return [
                'label' => 'Warning',
                'bg' => '#fef3c7',     // Kuning/Oranye (Warning)
                'text' => '#b45309',
                'border' => '#fcd34d',
            ];
        }

        return [
            'label' => 'Danger',
            'bg' => '#fee2e2',         // Merah Muda/Terang (Danger - beberapa bulan lagi)
            'text' => '#dc2626',
            'border' => '#fca5a5',
        ];
    }
}
