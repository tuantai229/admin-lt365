<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class NewUsersChart extends ChartWidget
{
    protected static ?string $heading = 'Người dùng mới';

    protected function getData(): array
    {
        $data = User::selectRaw('COUNT(*) as count, MONTH(created_at) as month')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->pluck('count', 'month')
            ->all();

        return [
            'datasets' => [
                [
                    'label' => 'Người dùng mới',
                    'data' => array_values($data),
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
