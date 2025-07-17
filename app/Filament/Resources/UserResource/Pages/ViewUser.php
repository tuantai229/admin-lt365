<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Grid;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Thông tin cá nhân')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                ImageEntry::make('avatar')
                                    ->label('Ảnh đại diện')
                                    ->circular()
                                    ->size(100)
                                    ->defaultImageUrl(function ($record) {
                                        $default = match($record->gender) {
                                            'female' => asset('images/default-avatar-female.png'),
                                            'male' => asset('images/default-avatar-male.png'),
                                            default => asset('images/default-avatar.png')
                                        };
                                        return $default;
                                    }),
                                TextEntry::make('full_name')
                                    ->label('Họ và tên')
                                    ->weight('bold')
                                    ->size('lg'),
                                TextEntry::make('email')
                                    ->label('Email')
                                    ->copyable()
                                    ->icon('heroicon-o-envelope'),
                            ]),
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('phone')
                                    ->label('Số điện thoại')
                                    ->placeholder('Chưa có')
                                    ->copyable()
                                    ->icon('heroicon-o-phone'),
                                TextEntry::make('gender')
                                    ->label('Giới tính')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'male' => 'info',
                                        'female' => 'success',
                                        'other' => 'warning',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'male' => 'Nam',
                                        'female' => 'Nữ',
                                        'other' => 'Khác',
                                        default => 'Chưa xác định',
                                    }),
                                TextEntry::make('age')
                                    ->label('Tuổi')
                                    ->getStateUsing(function ($record) {
                                        return $record->age ? $record->age . ' tuổi' : 'Chưa xác định';
                                    })
                                    ->icon('heroicon-o-calendar'),
                            ]),
                        TextEntry::make('address')
                            ->label('Địa chỉ')
                            ->placeholder('Chưa có')
                            ->columnSpanFull(),
                        TextEntry::make('bio')
                            ->label('Giới thiệu')
                            ->placeholder('Chưa có')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Section::make('Thông tin tài khoản')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                IconEntry::make('status')
                                    ->label('Trạng thái')
                                    ->boolean()
                                    ->tooltip(fn ($record) => $record->status ? 'Đang hoạt động' : 'Tạm khóa'),
                                IconEntry::make('email_verified_at')
                                    ->label('Xác thực email')
                                    ->boolean()
                                    ->getStateUsing(fn ($record) => !is_null($record->email_verified_at))
                                    ->tooltip(fn ($record) => $record->email_verified_at ? 
                                        'Đã xác thực: ' . $record->email_verified_at->format('d/m/Y H:i') : 
                                        'Chưa xác thực email'),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Ngày đăng ký')
                                    ->dateTime('d/m/Y H:i')
                                    ->icon('heroicon-o-calendar-days'),
                                TextEntry::make('last_login_at')
                                    ->label('Đăng nhập cuối')
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('Chưa đăng nhập')
                                    ->icon('heroicon-o-clock'),
                            ]),
                    ])
                    ->columns(2),

                Section::make('Thống kê hoạt động')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('favorites_count')
                                    ->label('Yêu thích')
                                    ->getStateUsing(fn ($record) => $record->favorites()->count())
                                    ->badge()
                                    ->color('warning'),
                                TextEntry::make('downloads_count')
                                    ->label('Tải xuống')
                                    ->getStateUsing(fn ($record) => $record->downloads()->count())
                                    ->badge()
                                    ->color('info'),
                                TextEntry::make('ratings_count')
                                    ->label('Đánh giá')
                                    ->getStateUsing(fn ($record) => $record->ratings()->count())
                                    ->badge()
                                    ->color('success'),
                                TextEntry::make('comments_count')
                                    ->label('Bình luận')
                                    ->getStateUsing(fn ($record) => $record->comments()->count())
                                    ->badge()
                                    ->color('primary'),
                            ]),
                    ])
                    ->columns(4),
            ]);
    }
}