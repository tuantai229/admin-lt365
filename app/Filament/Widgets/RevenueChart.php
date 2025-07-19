<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class RevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Doanh thu';

    protected function getData(): array
    {
        $data = Order::selectRaw('SUM(total_amount) as total, MONTH(created_at) as month')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->all();

        return [
            'datasets' => [
                [
                    'label' => 'Doanh thu theo tháng',
                    'data' => array_values($data),
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
