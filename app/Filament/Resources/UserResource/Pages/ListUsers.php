<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tất cả')
                ->badge($this->getModel()::count()),
            'active' => Tab::make('Đang hoạt động')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 1))
                ->badge($this->getModel()::where('status', 1)->count()),
            'inactive' => Tab::make('Tạm khóa')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 0))
                ->badge($this->getModel()::where('status', 0)->count()),
            'verified' => Tab::make('Đã xác thực')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('email_verified_at'))
                ->badge($this->getModel()::whereNotNull('email_verified_at')->count()),
            'unverified' => Tab::make('Chưa xác thực')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('email_verified_at'))
                ->badge($this->getModel()::whereNull('email_verified_at')->count()),
            'recent' => Tab::make('Mới đăng ký')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('created_at', '>=', now()->subDays(30)))
                ->badge($this->getModel()::where('created_at', '>=', now()->subDays(30))->count()),
        ];
    }
}