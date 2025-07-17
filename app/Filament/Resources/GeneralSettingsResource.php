<?php

namespace App\Filament\Resources;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;

class GeneralSettingsResource extends Resource
{
    protected static ?string $model = Setting::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    
    protected static ?string $navigationLabel = 'Cài đặt chung';
    
    protected static ?string $navigationGroup = 'Cài đặt';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        $generalSettings = Setting::getGeneralSettings();
        $footerCategoryLinks = Setting::getFooterCategoryLinks();
        $footerSupportLinks = Setting::getFooterSupportLinks();

        return $form
            ->schema([
                Tabs::make('GeneralSettings')
                    ->tabs([
                        Tabs\Tab::make('Thông tin cơ bản')
                            ->schema([
                                Section::make('Thông tin website')
                                    ->schema([
                                        TextInput::make('domain')
                                            ->label('Domain')
                                            ->default($generalSettings['domain'] ?? '')
                                            ->placeholder('lt365.vn'),
                                        
                                        TextInput::make('hotline')
                                            ->label('Hotline')
                                            ->default($generalSettings['hotline'] ?? '')
                                            ->placeholder('0987 654 321'),
                                        
                                        TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->default($generalSettings['email'] ?? '')
                                            ->placeholder('info@lt365.vn'),
                                        
                                        Textarea::make('address')
                                            ->label('Địa chỉ')
                                            ->default($generalSettings['address'] ?? '')
                                            ->placeholder('Số 123 Đường Cầu Giấy, Quận Cầu Giấy, Hà Nội')
                                            ->rows(3),
                                        
                                        TextInput::make('working_hours')
                                            ->label('Giờ làm việc')
                                            ->default($generalSettings['working_hours'] ?? '')
                                            ->placeholder('8:00 - 17:30, Thứ Hai - Thứ Bảy'),
                                        
                                        Textarea::make('footer_intro')
                                            ->label('Giới thiệu footer')
                                            ->default($generalSettings['footer_intro'] ?? '')
                                            ->placeholder('Cung cấp thông tin, tài liệu và tư vấn chuyên sâu...')
                                            ->rows(4),
                                    ])
                                    ->columns(2),
                            ]),

                        Tabs\Tab::make('Mạng xã hội')
                            ->schema([
                                Section::make('Liên kết mạng xã hội')
                                    ->schema([
                                        TextInput::make('facebook')
                                            ->label('Facebook Page')
                                            ->url()
                                            ->default($generalSettings['facebook'] ?? '')
                                            ->placeholder('https://facebook.com/lt365'),
                                        
                                        TextInput::make('youtube')
                                            ->label('Youtube Channel')
                                            ->url()
                                            ->default($generalSettings['youtube'] ?? '')
                                            ->placeholder('https://youtube.com/lt365'),
                                        
                                        TextInput::make('instagram')
                                            ->label('Instagram')
                                            ->url()
                                            ->default($generalSettings['instagram'] ?? '')
                                            ->placeholder('https://instagram.com/lt365'),
                                        
                                        TextInput::make('tiktok')
                                            ->label('Tiktok')
                                            ->url()
                                            ->default($generalSettings['tiktok'] ?? '')
                                            ->placeholder('https://tiktok.com/@lt365'),
                                    ])
                                    ->columns(2),
                            ]),

                        Tabs\Tab::make('Links Footer')
                            ->schema([
                                Section::make('Danh mục Footer')
                                    ->schema([
                                        Repeater::make('footer_category_links')
                                            ->label('Links danh mục footer')
                                            ->schema([
                                                TextInput::make('title')
                                                    ->label('Tiêu đề')
                                                    ->required()
                                                    ->placeholder('Thi vào lớp 1'),
                                                
                                                TextInput::make('url')
                                                    ->label('Đường dẫn')
                                                    ->required()
                                                    ->placeholder('/thi-vao-lop-1'),
                                            ])
                                            ->default($footerCategoryLinks)
                                            ->columns(2)
                                            ->addActionLabel('Thêm link danh mục')
                                            ->collapsible()
                                            ->cloneable(),
                                    ]),

                                Section::make('Hỗ trợ Footer')
                                    ->schema([
                                        Repeater::make('footer_support_links')
                                            ->label('Links hỗ trợ footer')
                                            ->schema([
                                                TextInput::make('title')
                                                    ->label('Tiêu đề')
                                                    ->required()
                                                    ->placeholder('Câu hỏi thường gặp'),
                                                
                                                TextInput::make('url')
                                                    ->label('Đường dẫn')
                                                    ->required()
                                                    ->placeholder('/faq'),
                                            ])
                                            ->default($footerSupportLinks)
                                            ->columns(2)
                                            ->addActionLabel('Thêm link hỗ trợ')
                                            ->collapsible()
                                            ->cloneable(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\GeneralSettingsResource\Pages\ManageGeneralSettings::route('/'),
        ];
    }
}