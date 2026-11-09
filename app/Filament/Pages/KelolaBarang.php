<?php

namespace App\Filament\Pages;

use App\Models\Barang;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\WithFileUploads;
use OpenSpout\Reader\Common\Creator\ReaderEntityFactory;

class KelolaBarang extends Page
{
    use WithFileUploads;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Kelola Barang';
    protected static ?string $title = 'Kelola Barang';
    protected static ?string $navigationGroup = 'Gudang & Inventori';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'kelola-barang';

    protected static string $view = 'filament.pages.kelola-barang';

    public bool $showImportModal = false;
    public $fileImport = null;

    public static function getNavigationBadge(): ?string
    {
        return (string) Barang::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public function getBarangsProperty()
    {
        return Barang::query()
            ->select([
                'id',
                'no',
                'nama_barang',
                'status_racikan',
                'jumlah',
                'tanggal_masuk',
                'tanggal_expired',
            ])
            ->orderBy('no', 'asc')
            ->get();
    }

    public function bukaModalImport(): void
    {
        $this->fileImport = null;
        $this->showImportModal = true;
    }

    public function tutupModalImport(): void
    {
        $this->showImportModal = false;
    }

    public function prosesImportFile(): void
    {
        $this->validate([
            'fileImport' => 'required|file|max:10240',
        ], [
            'fileImport.required' => 'Pilih file CSV atau Excel (.xlsx) terlebih dahulu.',
        ]);

        $path = $this->fileImport->getRealPath();
        $ext = strtolower($this->fileImport->getClientOriginalExtension());

        $countImported = 0;

        if ($ext === 'csv' || $ext === 'txt') {
            $handle = fopen($path, 'r');
            if ($handle) {
                // Lewati baris header
                fgetcsv($handle);
                while (($row = fgetcsv($handle, 2000, ',')) !== false) {
                    if ($this->simpanBarisImport($row)) {
                        $countImported++;
                    }
                }
                fclose($handle);
            }
        } elseif ($ext === 'xlsx') {
            $reader = ReaderEntityFactory::createXLSXReader();
            $reader->open($path);
            foreach ($reader->getSheetIterator() as $sheet) {
                $isFirst = true;
                foreach ($sheet->getRowIterator() as $rowObj) {
                    if ($isFirst) {
                        $isFirst = false;
                        continue;
                    }
                    $cells = array_map(fn ($c) => $c->getValue(), $rowObj->getCells());
                    if ($this->simpanBarisImport($cells)) {
                        $countImported++;
                    }
                }
                break;
            }
            $reader->close();
        } else {
            Notification::make()
                ->title('Format file harus berupa .csv atau .xlsx')
                ->danger()
                ->send();
            return;
        }

        $this->showImportModal = false;
        $this->fileImport = null;

        Notification::make()
            ->title("Berhasil mengimport {$countImported} data barang!")
            ->success()
            ->send();

        $this->redirect(static::getUrl(), navigate: true);
    }

    protected function simpanBarisImport(array $row): bool
    {
        if (count($row) < 2 || empty(trim((string) ($row[1] ?? '')))) {
            return false;
        }

        $no = trim((string) ($row[0] ?? ''));
        if ($no === '') {
            $no = str_pad((string) (Barang::count() + 1), 3, '0', STR_PAD_LEFT);
        }

        $namaBarang = trim((string) ($row[1] ?? ''));
        $tglMasuk = $this->parseDateValue($row[2] ?? null) ?? now()->format('Y-m-d');
        $kategori = trim((string) ($row[3] ?? 'Lainnya'));
        if (strtolower($kategori) === 'makser') {
            $kategori = 'Masker';
        }

        $alokasi = trim((string) ($row[4] ?? 'Gudang Pusat Lt 1'));
        $supplier = trim((string) ($row[5] ?? '-'));
        $jumlah = (int) ($row[6] ?? 0);
        $tglExpired = $this->parseDateValue($row[7] ?? null) ?? now()->addYear()->format('Y-m-d');
        $satuan = trim((string) ($row[8] ?? 'pcs')) ?: 'pcs';
        $harga = (float) preg_replace('/[^0-9.]/', '', (string) ($row[9] ?? 0));

        $statusRacikan = strtolower($kategori) === 'bahan racik'
            ? 'Bahan Racik'
            : 'Non Racikan';

        Barang::updateOrCreate(
            ['no' => $no, 'nama_barang' => $namaBarang],
            [
                'status_racikan' => $statusRacikan,
                'tanggal_masuk' => $tglMasuk,
                'kategori' => $kategori,
                'alokasi_penyimpanan' => $alokasi,
                'supplier' => $supplier,
                'jumlah' => $jumlah,
                'tanggal_expired' => $tglExpired,
                'satuan' => $satuan,
                'harga_barang' => $harga,
            ]
        );

        return true;
    }

    protected function parseDateValue($val): ?string
    {
        if ($val instanceof \DateTimeInterface) {
            return Carbon::instance($val)->format('Y-m-d');
        }
        $str = trim((string) $val);
        if ($str === '') {
            return null;
        }
        try {
            return Carbon::parse($str)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    public function hapusBarang(int $id): void
    {
        $barang = Barang::find($id);

        if ($barang) {
            $nama = $barang->nama_barang;
            $barang->delete();

            Notification::make()
                ->title("Barang '{$nama}' berhasil dihapus.")
                ->success()
                ->send();
        }

        $this->redirect(static::getUrl(), navigate: true);
    }
}
