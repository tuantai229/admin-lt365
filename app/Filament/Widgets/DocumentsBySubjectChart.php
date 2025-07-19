<?php

namespace App\Filament\Widgets;

use App\Models\Subject;
use Filament\Widgets\ChartWidget;

class DocumentsBySubjectChart extends ChartWidget
{
    protected static ?string $heading = 'Phân bố tài liệu theo môn học';

    protected function getData(): array
    {
        $subjects = Subject::withCount('documents')->get();

        return [
            'datasets' => [
                [
                    'label' => 'Số lượng tài liệu',
                    'data' => $subjects->pluck('documents_count')->all(),
                ],
            ],
            'labels' => $subjects->pluck('name')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
