<x-filament-panels::page>
    <div class="mb-2 flex items-center justify-between">
        <a href="{{ \App\Filament\Pages\KelolaBarang::getUrl() }}" wire:navigate
           class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400">
            <x-filament::icon icon="heroicon-m-arrow-left" class="w-4 h-4" />
            Kembali ke Tabel Kelola Barang
        </a>
    </div>

    <form wire:submit="simpan" class="space-y-6">
        {{ $this->form }}

        <div class="flex items-center justify-end gap-3">
            <x-filament::button
                color="gray"
                tag="a"
                href="{{ \App\Filament\Pages\KelolaBarang::getUrl() }}"
                wire:navigate
            >
                Batal
            </x-filament::button>

            <x-filament::button type="submit" icon="heroicon-m-check">
                Simpan Data Barang
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
