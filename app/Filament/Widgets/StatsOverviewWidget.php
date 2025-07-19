<?php

namespace App\Filament\Widgets;

use App\Models\Document;
use App\Models\School;
use App\Models\News;
use App\Models\Teacher;
use App\Models\Center;
use App\Models\User;
use App\Models\Order;
use App\Models\UserDownload;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Tổng số tài liệu/đề thi', Document::count())
                ->description('Tổng số tài liệu')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Tổng số trường học', School::count())
                ->description('Tổng số trường học')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Tổng số tin tức', News::count())
                ->description('Tổng số tin tức')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Tổng số giáo viên/trung tâm', Teacher::count() + Center::count())
                ->description('Tổng số giáo viên/trung tâm')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Tổng số người dùng', User::count())
                ->description('Tổng số người dùng')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Tổng số đơn hàng', Order::count())
                ->description('Tổng số đơn hàng')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Doanh thu tháng này', Order::whereMonth('created_at', Carbon::now()->month)->sum('total_amount'))
                ->description('Doanh thu tháng này')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Số lượt tải xuống', UserDownload::count())
                ->description('Số lượt tải xuống')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];
    }
}
