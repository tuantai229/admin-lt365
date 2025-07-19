<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DocumentsByLevelChart;
use App\Filament\Widgets\DocumentsBySubjectChart;
use App\Filament\Widgets\NewCommentsWidget;
use App\Filament\Widgets\NewContactsWidget;
use App\Filament\Widgets\NewDocumentsWidget;
use App\Filament\Widgets\NewNewslettersWidget;
use App\Filament\Widgets\NewUsersChart;
use App\Filament\Widgets\RevenueChart;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\TopDocumentsWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationGroup = 'Dashboard';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Bảng điều khiển';

    public function getWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
            RevenueChart::class,
            TopDocumentsWidget::class,
            DocumentsByLevelChart::class,
            DocumentsBySubjectChart::class,
            NewUsersChart::class,
            NewDocumentsWidget::class,
            NewCommentsWidget::class,
            NewContactsWidget::class,
            NewNewslettersWidget::class,
        ];
    }
}
