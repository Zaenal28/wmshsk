<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard';

    public function getSubheading(): ?string
    {
        $userName = auth()->user()->name ?? 'Gudang Pusat';
        return "Selamat Datang {$userName}";
    }
}
