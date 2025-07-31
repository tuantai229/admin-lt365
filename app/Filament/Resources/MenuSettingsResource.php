<?php

namespace App\Filament\Resources;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;

class MenuSettingsResource extends Resource
{
    protected static ?string $model = Setting::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-bars-3';
    
    protected static ?string $navigationLabel = 'Cài đặt menu';
    
    protected static ?string $navigationGroup = 'Cài đặt';
    
    protected static ?int $navigationSort = 2;

    // Kiểm tra permission
    public static function shouldRegisterNavigation(): bool
    {
        return auth('admin')->user()->can('view_any_settings');
    }

    public static function canViewAny(): bool
    {
        return auth('admin')->user()->can('view_any_settings');
    }

    public static function form(Form $form): Form
    {
        $mainNavigation = Setting::getMainNavigation();

        return $form
            ->schema([
                Section::make('Cấu hình Menu Navigation')
                    ->description('Cấu hình menu điều hướng chính của website')
                    ->schema([
                        Repeater::make('main_navigation')
                            ->label('Menu chính')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('title')
                                                    ->label('🔹 Tiêu đề menu chính')
                                                    ->required()
                                                    ->placeholder('Trang chủ')
                                                    ->extraAttributes(['style' => 'font-weight: bold; color: #1e40af;']),
                                                
                                                TextInput::make('url')
                                                    ->label('🔗 Đường dẫn')
                                                    ->required()
                                                    ->placeholder('/')
                                                    ->helperText('Sử dụng # nếu chỉ là menu cha'),
                                            ]),
                                        
                                        Repeater::make('children')
                                            ->label('📂 Menu con cấp 1')
                                            ->schema([
                                                Section::make()
                                                    ->schema([
                                                        Grid::make(2)
                                                            ->schema([
                                                                TextInput::make('title')
                                                                    ->label('📌 Tiêu đề cấp 1')
                                                                    ->required()
                                                                    ->placeholder('Thi vào lớp 1')
                                                                    ->extraAttributes(['style' => 'font-weight: 600; color: #059669; margin-left: 20px;']),
                                                                
                                                                TextInput::make('url')
                                                                    ->label('🔗 Đường dẫn')
                                                                    ->required()
                                                                    ->placeholder('/thi-vao-lop-1'),
                                                            ]),
                                                        
                                                        Repeater::make('children')
                                                            ->label('📄 Menu con cấp 2')
                                                            ->schema([
                                                                Section::make()
                                                                    ->schema([
                                                                        Grid::make(2)
                                                                            ->schema([
                                                                                TextInput::make('title')
                                                                                    ->label('▪️ Tiêu đề cấp 2')
                                                                                    ->required()
                                                                                    ->placeholder('Thông tin')
                                                                                    ->extraAttributes(['style' => 'color: #dc2626; margin-left: 40px;']),
                                                                                
                                                                                TextInput::make('url')
                                                                                    ->label('🔗 Đường dẫn')
                                                                                    ->required()
                                                                                    ->placeholder('/thi-vao-lop-1/thong-tin'),
                                                                            ]),
                                                                    ])
                                                                    ->extraAttributes(['style' => 'background-color: #fef3f2; border-left: 4px solid #dc2626; margin-left: 20px; padding: 10px;'])
                                                            ])
                                                            ->defaultItems(0)
                                                            ->addActionLabel('➕ Thêm menu cấp 2')
                                                            ->collapsible()
                                                            ->collapsed()
                                                            ->extraAttributes(['style' => 'margin-left: 20px;']),
                                                    ])
                                                    ->extraAttributes(['style' => 'background-color: #f0fdf4; border-left: 4px solid #059669; margin-left: 10px; padding: 10px;'])
                                            ])
                                            ->defaultItems(0)
                                            ->addActionLabel('➕ Thêm menu cấp 1')
                                            ->collapsible()
                                            ->collapsed()
                                            ->itemLabel(fn (array $state): ?string => '📌 ' . ($state['title'] ?? 'Menu con'))
                                            ->extraAttributes(['style' => 'margin-left: 10px;']),
                                    ])
                                    ->extraAttributes(['style' => 'background-color: #eff6ff; border-left: 4px solid #1e40af; padding: 15px; margin-bottom: 10px;'])
                            ])
                            ->default($mainNavigation)
                            ->addActionLabel('➕ Thêm menu chính')
                            ->collapsible()
                            ->cloneable()
                            ->itemLabel(fn (array $state): ?string => '🔹 ' . ($state['title'] ?? 'Menu chính'))
                            ->reorderableWithButtons()
                            ->extraAttributes(['style' => 'border: 2px solid #e5e7eb; border-radius: 8px; padding: 10px;']),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\MenuSettingsResource\Pages\ManageMenuSettings::route('/'),
        ];
    }
}
