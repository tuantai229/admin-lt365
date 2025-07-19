<?php

namespace App\Filament\Widgets;

use App\Models\Level;
use Filament\Widgets\ChartWidget;

class DocumentsByLevelChart extends ChartWidget
{
    protected static ?string $heading = 'Phân bố tài liệu theo cấp học';

    protected function getData(): array
    {
        $levels = Level::where('parent_id', 0)->withCount('documents')->get();

        return [
            'datasets' => [
                [
                    'label' => 'Số lượng tài liệu',
                    'data' => $levels->pluck('documents_count')->all(),
                ],
            ],
            'labels' => $levels->pluck('name')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
