<?php

namespace App\Filament\Widgets;

use App\Models\Barang;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsBarangOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalSku = Barang::count();
        $stokAman = Barang::where('jumlah', '>', 50)->count();
        $stokMenipis = Barang::whereBetween('jumlah', [1, 50])->count();
        $stokHabis = Barang::where('jumlah', '<=', 0)->count();

        return [
            Stat::make('Total SKU', number_format($totalSku, 0, ',', '.'))
                ->description('Total item barang terdaftar')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),

            Stat::make('Stok Aman', number_format($stokAman, 0, ',', '.'))
                ->description('Jumlah stok > 50')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Stok Menipis', number_format($stokMenipis, 0, ',', '.'))
                ->description('Jumlah stok 1 - 50')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),

            Stat::make('Stok Habis', number_format($stokHabis, 0, ',', '.'))
                ->description('Jumlah stok 0 / kosong')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
