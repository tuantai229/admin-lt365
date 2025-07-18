<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationGroup = 'Dashboard';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Bảng điều khiển';
}
