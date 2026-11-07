<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class StockOpname extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Stock Opname';
    protected static ?string $title = 'Stock Opname';
    protected static ?string $navigationGroup = 'Audit & Penyesuaian';
    protected static ?int $navigationSort = 4;
    protected static ?string $slug = 'stock-opname';

    protected static string $view = 'filament.pages.stock-opname';

    public static function getNavigationBadge(): ?string
    {
        return '12';
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
