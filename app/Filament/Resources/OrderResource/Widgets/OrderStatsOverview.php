<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Tổng số đơn hàng', Order::count()),
            Stat::make('Đơn hàng chờ xử lý', Order::where('status', 'pending')->count()),
            Stat::make('Doanh thu', number_format(Order::where('payment_status', 'paid')->sum('total_amount')) . ' VND'),
        ];
    }
}
