<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use App\Filament\Resources\DocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewDocument extends ViewRecord
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
            Actions\Action::make('download')
                ->label('Tải xuống')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->url(fn (): string => route('documents.download', $this->record))
                ->openUrlInNewTab()
                ->visible(fn (): bool => $this->record->hasFile()),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Thông tin cơ bản')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Tên tài liệu'),
                        
                        Infolists\Components\TextEntry::make('slug')
                            ->label('Slug'),
                        
                        Infolists\Components\TextEntry::make('description')
                            ->label('Mô tả')
                            ->columnSpanFull(),
                        
                        Infolists\Components\ImageEntry::make('featuredImage.path')
                            ->label('Ảnh đại diện')
                            ->disk('public')
                            ->height(200)
                            ->defaultImageUrl(url('/images/placeholder.png')),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Phân loại')
                    ->schema([
                        Infolists\Components\TextEntry::make('level.name')
                            ->label('Cấp học')
                            ->placeholder('--'),
                        
                        Infolists\Components\TextEntry::make('subject.name')
                            ->label('Môn học')
                            ->placeholder('--'),
                        
                        Infolists\Components\TextEntry::make('documentType.name')
                            ->label('Loại tài liệu')
                            ->placeholder('--'),
                        
                        Infolists\Components\TextEntry::make('difficultyLevel.name')
                            ->label('Mức độ khó')
                            ->placeholder('--'),
                        
                        Infolists\Components\TextEntry::make('school.name')
                            ->label('Trường học')
                            ->placeholder('--'),
                        
                        Infolists\Components\TextEntry::make('year')
                            ->label('Năm')
                            ->placeholder('--'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('File & Thống kê')
                    ->schema([
                        Infolists\Components\TextEntry::make('formatted_file_size')
                            ->label('Kích thước file'),
                        
                        Infolists\Components\TextEntry::make('file_type')
                            ->label('Loại file')
                            ->placeholder('--'),
                        
                        Infolists\Components\TextEntry::make('formatted_price')
                            ->label('Giá')
                            ->badge()
                            ->color(fn (string $state): string => $state === 'Miễn phí' ? 'success' : 'primary'),
                        
                        Infolists\Components\TextEntry::make('download_count')
                            ->label('Lượt tải')
                            ->numeric(),
                        
                        Infolists\Components\IconEntry::make('is_featured')
                            ->label('Nổi bật')
                            ->boolean()
                            ->trueIcon('heroicon-o-star')
                            ->falseIcon('heroicon-o-star')
                            ->trueColor('warning')
                            ->falseColor('gray'),
                        
                        Infolists\Components\TextEntry::make('status_text')
                            ->label('Trạng thái')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Nháp' => 'secondary',
                                'Đã xuất bản' => 'success',
                                'Ẩn' => 'danger',
                                default => 'secondary',
                            }),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Tags')
                    ->schema([
                        Infolists\Components\TextEntry::make('tags.name')
                            ->label('Tags')
                            ->badge()
                            ->separator(',')
                            ->placeholder('Không có tags'),
                    ])
                    ->visible(fn (): bool => $this->record->tags->isNotEmpty()),

                Infolists\Components\Section::make('Nội dung')
                    ->schema([
                        Infolists\Components\TextEntry::make('content')
                            ->label('Nội dung chi tiết')
                            ->html()
                            ->placeholder('Không có nội dung'),
                    ])
                    ->visible(fn (): bool => !empty($this->record->content)),

                Infolists\Components\Section::make('Thông tin khác')
                    ->schema([
                        Infolists\Components\TextEntry::make('adminUser.name')
                            ->label('Người tạo')
                            ->placeholder('--'),
                        
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Ngày tạo')
                            ->dateTime('d/m/Y H:i'),
                        
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Ngày cập nhật')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(3),
            ]);
    }
}