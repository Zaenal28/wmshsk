<?php

namespace App\Filament\Pages;

use App\Models\Barang;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class EditBarang extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static ?string $title = 'Edit Data Barang';
    protected static ?string $slug = 'kelola-barang/{record}/edit';

    // Sembunyikan dari sidebar karena diakses melalui tombol "Edit" di tabel Kelola Barang
    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.edit-barang';

    public Barang $barang;

    public ?array $data = [];

    public function mount(int|string $record): void
    {
        $this->barang = Barang::findOrFail($record);

        $this->form->fill([
            'no' => $this->barang->no,
            'supplier' => $this->barang->supplier,
            'nama_barang' => $this->barang->nama_barang,
            'jumlah' => $this->barang->jumlah,
            'tanggal_masuk' => $this->barang->tanggal_masuk?->format('Y-m-d'),
            'tanggal_expired' => $this->barang->tanggal_expired?->format('Y-m-d'),
            'kategori' => $this->barang->kategori,
            'alokasi_penyimpanan' => $this->barang->alokasi_penyimpanan,
            'satuan' => $this->barang->satuan,
            'harga_barang' => $this->barang->harga_barang,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Form Edit Data Barang')
                    ->description('Perbarui informasi detail barang di Gudang Pusat.')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('no')
                                ->label('No')
                                ->required()
                                ->maxLength(50),

                            TextInput::make('supplier')
                                ->label('Supplier')
                                ->required()
                                ->maxLength(150),

                            TextInput::make('nama_barang')
                                ->label('Nama Barang')
                                ->required()
                                ->maxLength(200),

                            TextInput::make('jumlah')
                                ->label('Jumlah')
                                ->numeric()
                                ->required()
                                ->minValue(0),

                            DatePicker::make('tanggal_masuk')
                                ->label('Tanggal Masuk')
                                ->required()
                                ->native(false)
                                ->displayFormat('d/m/y'),

                            DatePicker::make('tanggal_expired')
                                ->label('Tanggal Expired')
                                ->required()
                                ->native(false)
                                ->displayFormat('d/m/y'),

                            Select::make('kategori')
                                ->label('Jenis atau Kategori Barang')
                                ->options(fn (): array => Barang::getKategoriOptions())
                                ->searchable()
                                ->preload()
                                ->required()
                                ->createOptionForm([
                                    TextInput::make('nama_kategori')
                                        ->label('Tambah Kategori Baru')
                                        ->placeholder('Ketik nama jenis / kategori baru...')
                                        ->required()
                                        ->maxLength(100),
                                ])
                                ->createOptionUsing(function (array $data): string {
                                    $kategoriBaru = trim($data['nama_kategori']);
                                    $custom = session('custom_kategori_barang', []);
                                    $custom[$kategoriBaru] = $kategoriBaru;
                                    session(['custom_kategori_barang' => $custom]);

                                    return $kategoriBaru;
                                }),

                            Select::make('satuan')
                                ->label('Satuan')
                                ->options([
                                    'Pcs' => 'Pcs',
                                    'Box' => 'Box',
                                    'Botol' => 'Botol',
                                    'Tube' => 'Tube',
                                    'Pot' => 'Pot',
                                    'Karton' => 'Karton',
                                    'Roll' => 'Roll',
                                ])
                                ->searchable()
                                ->required(),

                            Select::make('alokasi_penyimpanan')
                                ->label('Alokasi Penyimpanan')
                                ->options(fn (): array => Barang::getAlokasiPenyimpananOptions())
                                ->searchable()
                                ->preload()
                                ->required()
                                ->createOptionForm([
                                    TextInput::make('nama_alokasi')
                                        ->label('Tambah Alokasi Penyimpanan Baru')
                                        ->placeholder('Contoh: Gudang Pusat Lt 2 / Rak A1...')
                                        ->required()
                                        ->maxLength(150),
                                ])
                                ->createOptionUsing(function (array $data): string {
                                    $alokasiBaru = trim($data['nama_alokasi']);
                                    $custom = session('custom_alokasi_penyimpanan', []);
                                    $custom[$alokasiBaru] = $alokasiBaru;
                                    session(['custom_alokasi_penyimpanan' => $custom]);

                                    return $alokasiBaru;
                                }),

                            TextInput::make('harga_barang')
                                ->label('Harga Barang')
                                ->prefix('Rp')
                                ->numeric()
                                ->required()
                                ->minValue(0),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function update(): void
    {
        $validated = $this->form->getState();

        $this->barang->update($validated);

        Notification::make()
            ->title('Data barang berhasil diperbarui!')
            ->success()
            ->send();

        $this->redirect(KelolaBarang::getUrl(), navigate: true);
    }
}
