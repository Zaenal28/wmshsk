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
        'status_racikan',
        'jumlah',
        'tanggal_masuk',
        'tanggal_expired',
        'kategori',
        'alokasi_penyimpanan',
        'satuan',
        'harga_barang',
        'cabang',
        'user_id',
    ];

    protected $appends = [
        'masa_berlaku',
        'status_expired_info',
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

    public static function getStatusRacikanOptions(): array
    {
        return [
            'Non Racikan' => 'Non Racikan (Barang Biasa Siap Kirim)',
            'Sudah Diracik' => 'Sudah Diracik (Barang Racikan Siap Kirim)',
            'Belum Diracik' => 'Belum Diracik (Barang Dasar / Base)',
            'Bahan Racik' => 'Bahan Racik (Bahan Campuran Meracik)',
        ];
    }

    public function getStatusRacikanBadgeAttribute(): array
    {
        return match ($this->status_racikan) {
            'Sudah Diracik' => [
                'label' => 'Sudah Diracik',
                'bg' => '#dbeafe',
                'text' => '#1d4ed8',
                'border' => '#93c5fd',
            ],
            'Belum Diracik' => [
                'label' => 'Belum Diracik',
                'bg' => '#fef3c7',
                'text' => '#b45309',
                'border' => '#fcd34d',
            ],
            'Bahan Racik' => [
                'label' => 'Bahan Racik',
                'bg' => '#f3e8ff',
                'text' => '#7e22ce',
                'border' => '#d8b4fe',
            ],
            default => [
                'label' => 'Non Racikan',
                'bg' => '#f1f5f9',
                'text' => '#475569',
                'border' => '#cbd5e1',
            ],
        };
    }

    protected static function booted(): void
    {
        // Otomatis sinkronkan ke Perencanaan Stok saat barang baru ditambahkan
        static::created(function (Barang $barang) {
            $standardName = PerencanaanStok::standardizeProductName($barang->nama_barang);
            $currentPeriode = date('Y-m');

            // Ambil periode berjalan dan periode mendatang yang ada di database
            $periodes = PerencanaanStok::select('periode')
                ->where('periode', '>=', $currentPeriode)
                ->distinct()
                ->pluck('periode');

            if ($periodes->isEmpty()) {
                $periodes = collect([$currentPeriode]);
            }

            foreach ($periodes as $p) {
                PerencanaanStok::updateOrCreate(
                    [
                        'periode' => $p,
                        'tipe_periode' => 'bulanan',
                        'nama_barang' => $standardName,
                    ],
                    [
                        'barang_id' => $barang->id,
                        'no_urut' => (string) $barang->no,
                        'kategori' => $barang->kategori ?: 'Lain-lain',
                        'stok_awal' => (int) $barang->jumlah,
                        'stok_akhir' => (int) $barang->jumlah,
                        'expired_1' => $barang->tanggal_expired ? Carbon::parse($barang->tanggal_expired)->format('n/Y') : null,
                        'status_pesan' => 'Tidak',
                        'tanggal_mulai' => Carbon::parse($p . '-01')->startOfMonth()->toDateString(),
                        'tanggal_selesai' => Carbon::parse($p . '-01')->endOfMonth()->toDateString(),
                    ]
                );
            }
        });

        // Otomatis perbarui Perencanaan Stok saat barang diedit di Kelola Barang
        static::updated(function (Barang $barang) {
            $oldName = $barang->getOriginal('nama_barang');
            $standardOldName = $oldName ? PerencanaanStok::standardizeProductName($oldName) : null;
            $standardNewName = PerencanaanStok::standardizeProductName($barang->nama_barang);

            // Cari semua perencanaan_stoks yang terkait dengan barang ini
            $query = PerencanaanStok::where('barang_id', $barang->id);
            if ($standardOldName) {
                $query->orWhere('nama_barang', $oldName)
                      ->orWhere('nama_barang', $standardOldName);
            }

            $query->update([
                'barang_id' => $barang->id,
                'nama_barang' => $standardNewName,
                'kategori' => $barang->kategori ?: 'Lain-lain',
                'no_urut' => (string) $barang->no,
            ]);

            // Jika tanggal expired diubah, perbarui expired_1 di periode berjalan & masa depan
            if ($barang->isDirty('tanggal_expired') && $barang->tanggal_expired) {
                $formattedExp = Carbon::parse($barang->tanggal_expired)->format('n/Y');
                PerencanaanStok::where('barang_id', $barang->id)
                    ->where('periode', '>=', date('Y-m'))
                    ->update(['expired_1' => $formattedExp]);
            }
        });

        // Hapus dari periode aktif & masa depan jika barang dihapus dari master
        static::deleted(function (Barang $barang) {
            $standardName = PerencanaanStok::standardizeProductName($barang->nama_barang);
            PerencanaanStok::where(function ($q) use ($barang, $standardName) {
                $q->where('barang_id', $barang->id)
                  ->orWhere('nama_barang', $barang->nama_barang)
                  ->orWhere('nama_barang', $standardName);
            })
            ->where('periode', '>=', date('Y-m'))
            ->delete();
        });
    }

    /**
     * Daftar kategori default beserta kategori baru yang pernah diketik/ditambahkan ke database
     */
    public static function getKategoriOptions(): array
    {
        $orderedDefaults = [
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
            'Treatment Equiment' => 'Treatment Equiment',
            'Kebutuhan treatment' => 'Kebutuhan treatment',
            'KOREAN LUX' => 'KOREAN LUX',
            'Bahan Racik' => 'Bahan Racik',
            'bahan racik D10/ Penggnti LGNC' => 'bahan racik D10/ Penggnti LGNC',
            'LGNC' => 'LGNC',
            'SMO' => 'SMO',
            'DEEPORE' => 'DEEPORE',
            'Injeksi + dokter' => 'Injeksi + dokter',
        ];

        // Ambil kategori dari master barang
        $fromBarangs = static::query()
            ->whereNotNull('kategori')
            ->where('kategori', '!=', '')
            ->distinct()
            ->pluck('kategori', 'kategori')
            ->toArray();

        // Ambil kategori dari perencanaan stok agar selalu sinkron 100%
        $fromPerencanaan = PerencanaanStok::query()
            ->whereNotNull('kategori')
            ->where('kategori', '!=', '')
            ->distinct()
            ->pluck('kategori', 'kategori')
            ->toArray();

        $fromSession = session('custom_kategori_barang', []);

        // Gabungkan seluruh kategori unik
        $all = array_merge($orderedDefaults, $fromBarangs, $fromPerencanaan, $fromSession);

        return $all;
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
     * - Safe: >= 2 Tahun (>= 24 bulan)
     * - Warning: Menuju 1 Tahun (12 bulan s/d < 24 bulan)
     * - Danger: Beberapa Bulan Lagi (< 12 bulan dan belum melewati tanggal expired)
     * - Expired: Melebihi Tgl Expired (sudah melewati tanggal expired)
     */
    public function getStatusExpiredInfoAttribute(): array
    {
        if (! $this->tanggal_expired || ! $this->tanggal_masuk) {
            return [
                'label' => '-',
                'description' => '-',
                'bg' => '#f3f4f6',
                'text' => '#374151',
                'border' => '#d1d5db',
                'dot' => '#9ca3af',
                'class' => 'bg-slate-100 text-slate-700 border border-slate-300',
            ];
        }

        $acuan = Carbon::parse($this->tanggal_masuk)->startOfDay();
        $today = Carbon::now()->startOfDay();
        // Gunakan acuan tanggal saat ini terhadap tanggal expired
        $baseDate = $today->greaterThan($acuan) ? $today : $acuan;
        $expired = Carbon::parse($this->tanggal_expired)->startOfDay();

        if ($expired->lessThanOrEqualTo($baseDate)) {
            return [
                'label' => 'Expired',
                'description' => 'Expired (Melebihi Tgl Expired)',
                'bg' => '#7f1d1d',
                'text' => '#ffffff',
                'border' => '#991b1b',
                'dot' => '#fca5a5',
                'class' => 'bg-[#7f1d1d] text-white border border-[#991b1b]',
            ];
        }

        $totalMonths = ($baseDate->diff($expired)->y * 12) + $baseDate->diff($expired)->m;

        if ($totalMonths >= 24) {
            return [
                'label' => 'Safe',
                'description' => 'Safe (≥ 2 Tahun)',
                'bg' => '#ecfdf5',
                'text' => '#059669',
                'border' => '#a7f3d0',
                'dot' => '#10b981',
                'class' => 'bg-[#ecfdf5] text-[#059669] border border-[#a7f3d0]',
            ];
        }

        if ($totalMonths >= 12) {
            return [
                'label' => 'Warning',
                'description' => 'Warning (Menuju 1 Tahun)',
                'bg' => '#fffbeb',
                'text' => '#d97706',
                'border' => '#fde68a',
                'dot' => '#f59e0b',
                'class' => 'bg-[#fffbeb] text-[#d97706] border border-[#fde68a]',
            ];
        }

        return [
            'label' => 'Danger',
            'description' => 'Danger (Beberapa Bulan Lagi)',
            'bg' => '#fef2f2',
            'text' => '#e11d48',
            'border' => '#fecdd3',
            'dot' => '#f43f5e',
            'class' => 'bg-[#fef2f2] text-[#e11d48] border border-[#fecdd3]',
        ];
    }
}
