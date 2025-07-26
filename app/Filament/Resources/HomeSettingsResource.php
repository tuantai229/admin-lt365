<?php

namespace App\Filament\Resources;

use App\Models\Setting;
use App\Models\NewsCategory;
use App\Models\Center;
use App\Models\Teacher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Notifications\Notification;

class HomeSettingsResource extends Resource
{
    protected static ?string $model = Setting::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    protected static ?string $navigationLabel = 'Cài đặt trang chủ';
    
    protected static ?string $navigationGroup = 'Cài đặt';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('HomeSettings')
                    ->tabs([
                        Tabs\Tab::make('Hero Banner')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Section::make('Cấu hình Hero Banner Slider')
                                    ->description('Cấu hình các slide banner chính của trang chủ')
                                    ->schema([
                                        Repeater::make('hero_slides')
                                            ->label('Danh sách slides')
                                            ->schema([
                                                Grid::make(2)
                                                    ->schema([
                                                        FileUpload::make('image')
                                                            ->label('🖼️ Ảnh slide')
                                                            ->image()
                                                            ->directory('hero-slides')
                                                            ->imageEditor()
                                                            ->imageEditorAspectRatios([
                                                                '16:9',
                                                            ])
                                                            ->required()
                                                            ->helperText('Kích thước khuyến nghị: 1200x675px (16:9)'),
                                                        
                                                        TextInput::make('title')
                                                            ->label('📝 Tiêu đề chính')
                                                            ->required()
                                                            ->placeholder('Ví dụ: Đồng hành cùng con vào trường chuyên')
                                                            ->helperText('Tiêu đề lớn hiển thị trên slide'),
                                                    ]),
                                                
                                                Textarea::make('description')
                                                    ->label('📄 Mô tả ngắn')
                                                    ->required()
                                                    ->rows(3)
                                                    ->placeholder('Ví dụ: Cung cấp tài liệu, kinh nghiệm và tư vấn chuyên sâu...')
                                                    ->helperText('Mô tả chi tiết về nội dung slide'),
                                                
                                                Grid::make(2)
                                                    ->schema([
                                                        Section::make('Nút 1')
                                                            ->schema([
                                                                TextInput::make('button1_text')
                                                                    ->label('Văn bản nút 1')
                                                                    ->placeholder('Ví dụ: Tìm tài liệu')
                                                                    ->helperText('Nút bên trái'),
                                                                
                                                                TextInput::make('button1_url')
                                                                    ->label('Link nút 1')
                                                                    ->url()
                                                                    ->placeholder('https://example.com hoặc /trang-noi-bo'),
                                                                
                                                                Select::make('button1_color_class')
                                                                    ->label('Màu nền nút 1')
                                                                    ->options([
                                                                        'bg-white text-primary' => 'Trắng (Primary)',
                                                                        'bg-primary text-white' => 'Xanh Primary',
                                                                        'bg-secondary text-white' => 'Xanh Secondary',
                                                                        'bg-green-600 text-white' => 'Xanh lá',
                                                                        'bg-blue-600 text-white' => 'Xanh dương',
                                                                        'bg-purple-600 text-white' => 'Tím',
                                                                        'bg-red-600 text-white' => 'Đỏ',
                                                                    ])
                                                                    ->default('bg-white text-primary'),
                                                            ]),
                                                        
                                                        Section::make('Nút 2')
                                                            ->schema([
                                                                TextInput::make('button2_text')
                                                                    ->label('Văn bản nút 2')
                                                                    ->placeholder('Ví dụ: Đăng ký tư vấn')
                                                                    ->helperText('Nút bên phải'),
                                                                
                                                                TextInput::make('button2_url')
                                                                    ->label('Link nút 2')
                                                                    ->url()
                                                                    ->placeholder('https://example.com hoặc /trang-noi-bo'),
                                                                
                                                                Select::make('button2_color_class')
                                                                    ->label('Màu nền nút 2')
                                                                    ->options([
                                                                        'bg-secondary text-white' => 'Xanh Secondary',
                                                                        'bg-primary text-white' => 'Xanh Primary',
                                                                        'bg-white text-primary' => 'Trắng (Primary)',
                                                                        'bg-green-600 text-white' => 'Xanh lá',
                                                                        'bg-blue-600 text-white' => 'Xanh dương',
                                                                        'bg-purple-600 text-white' => 'Tím',
                                                                        'bg-red-600 text-white' => 'Đỏ',
                                                                    ])
                                                                    ->default('bg-secondary text-white'),
                                                            ]),
                                                    ]),
                                            ])
                                            ->default([])
                                            ->addActionLabel('➕ Thêm slide')
                                            ->collapsible()
                                            ->cloneable()
                                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Slide mới')
                                            ->reorderableWithButtons()
                                            ->helperText('Tối đa 5 slides để đảm bảo hiệu suất tốt'),
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Tabs\Tab::make('Chuyển cấp nhanh')
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                Section::make('Cấu hình phần Chuyển cấp nhanh')
                                    ->description('Cấu hình 3 box thông tin chính (Lớp 1, Lớp 6, Lớp 10)')
                                    ->schema([
                                        TextInput::make('quick_transfer_title')
                                            ->label('🏷️ Tiêu đề phần')
                                            ->placeholder('Ví dụ: Đồng hành cùng con vào trường chuyên')
                                            ->helperText('Tiêu đề lớn hiển thị trên đầu phần'),

                                        Repeater::make('quick_transfer_boxes')
                                            ->label('Danh sách box')
                                            ->schema([
                                                Grid::make(2)
                                                    ->schema([
                                                        FileUpload::make('image')
                                                            ->label('🖼️ Ảnh box')
                                                            ->image()
                                                            ->directory('quick-transfer')
                                                            ->imageEditor()
                                                            ->imageEditorAspectRatios([
                                                                '1:1',
                                                                '4:3',
                                                            ])
                                                            ->required()
                                                            ->helperText('Kích thước khuyến nghị: 400x300px'),

                                                        TextInput::make('title')
                                                            ->label('📝 Tiêu đề box')
                                                            ->required()
                                                            ->placeholder('Ví dụ: Thi vào lớp 1')
                                                            ->helperText('Tiêu đề của box'),
                                                    ]),

                                                Textarea::make('description')
                                                    ->label('📄 Mô tả box')
                                                    ->required()
                                                    ->rows(4)
                                                    ->placeholder('Mỗi dòng là một thông tin. Ví dụ:' . "\n" . '5 trường tiểu học hàng đầu Hà Nội' . "\n" . 'Lịch thi tuyển sinh năm 2025-2026' . "\n" . 'Bộ đề luyện thi mẫu cập nhật')
                                                    ->helperText('Mỗi dòng sẽ hiển thị với icon tick xanh'),

                                                TextInput::make('button_url')
                                                    ->label('🔗 Link nút "Xem thêm"')
                                                    ->url()
                                                    ->placeholder('https://example.com hoặc /trang-noi-bo')
                                                    ->helperText('Đường dẫn khi click nút "Xem thêm"'),
                                            ])
                                            ->default([])
                                            ->addActionLabel('➕ Thêm box')
                                            ->collapsible()
                                            ->cloneable()
                                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Box mới')  
                                            ->reorderableWithButtons()
                                            ->minItems(1)
                                            ->maxItems(6)
                                            ->helperText('Khuyến nghị 3 box cho hiển thị tối ưu'),
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Tabs\Tab::make('Tin tức & Lịch thi')
                            ->icon('heroicon-o-newspaper')
                            ->schema([
                                Section::make('Cấu hình phần Tin tức & Lịch thi')
                                    ->description('Chọn danh mục tin tức hiển thị trên trang chủ')
                                    ->schema([
                                        Select::make('news_category_id')
                                            ->label('📰 Chọn danh mục tin tuyển sinh')
                                            ->options(NewsCategory::where('status', 1)->pluck('name', 'id'))
                                            ->default(null)
                                            ->placeholder('Chọn danh mục tin tức...')
                                            ->helperText('Chọn danh mục tin tức sẽ hiển thị các bài viết mới nhất từ danh mục này')
                                            ->searchable()
                                            ->preload(),
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Tabs\Tab::make('Giáo viên & Trung tâm')
                            ->icon('heroicon-o-user-group')
                            ->schema([
                                Section::make('Cấu hình Trung tâm luyện thi')
                                    ->description('Chọn các trung tâm hiển thị trên trang chủ')
                                    ->schema([
                                        Select::make('selected_centers')
                                            ->label('🏫 Trung tâm luyện thi')
                                            ->multiple()
                                            ->options(Center::where('status', 1)->get()->mapWithKeys(function ($center) {
                                                return [$center->id => $center->name . ' - ' . ($center->tagline ?? 'Không có tagline')];
                                            }))
                                            ->default([])
                                            ->placeholder('Tìm và chọn trung tâm...')
                                            ->helperText('Tìm theo tên trung tâm. Có thể chọn nhiều trung tâm')
                                            ->searchable()
                                            ->preload(),
                                    ]),

                                Section::make('Cấu hình Giáo viên nổi bật')
                                    ->description('Chọn các giáo viên hiển thị trên trang chủ')
                                    ->schema([
                                        Select::make('selected_teachers')
                                            ->label('👨‍🏫 Giáo viên nổi bật')
                                            ->multiple()
                                            ->options(Teacher::where('status', 1)->get()->mapWithKeys(function ($teacher) {
                                                return [$teacher->id => $teacher->name . ' - ' . ($teacher->tagline ?? 'Không có tagline')];
                                            }))
                                            ->default([])
                                            ->placeholder('Tìm và chọn giáo viên...')
                                            ->helperText('Tìm theo tên giáo viên. Có thể chọn nhiều giáo viên')
                                            ->searchable()
                                            ->preload(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Thống kê & Đánh giá')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Section::make('Thống kê website')
                                    ->description('Cấu hình các số liệu thống kê')
                                    ->schema([
                                        Grid::make(4)
                                            ->schema([
                                                TextInput::make('stats_documents')
                                                    ->label('📚 Tài liệu')
                                                    ->placeholder('10,000+')
                                                    ->helperText('Số lượng tài liệu'),

                                                TextInput::make('stats_schools')
                                                    ->label('🏫 Trường học')
                                                    ->placeholder('500+')
                                                    ->helperText('Số lượng trường học'),

                                                TextInput::make('stats_members')
                                                    ->label('👥 Thành viên')
                                                    ->placeholder('50,000+')
                                                    ->helperText('Số lượng thành viên'),

                                                TextInput::make('stats_rating')
                                                    ->label('⭐ Đánh giá')
                                                    ->placeholder('4.8/5')
                                                    ->helperText('Điểm đánh giá trung bình'),
                                            ]),
                                    ]),

                                Section::make('Đánh giá từ phụ huynh')
                                    ->description('Cấu hình phần "Phụ huynh nói gì về chúng tôi"')
                                    ->schema([
                                        Repeater::make('parent_reviews')
                                            ->label('Danh sách đánh giá')
                                            ->schema([
                                                Grid::make(2)
                                                    ->schema([
                                                        FileUpload::make('avatar')
                                                            ->label('🖼️ Ảnh phụ huynh')
                                                            ->image()
                                                            ->directory('parent-reviews')
                                                            ->imageEditor()
                                                            ->imageEditorAspectRatios([
                                                                '1:1',
                                                            ])
                                                            ->required()
                                                            ->helperText('Kích thước khuyến nghị: 200x200px (1:1)'),

                                                        TextInput::make('name')
                                                            ->label('👤 Tên phụ huynh')
                                                            ->required()
                                                            ->placeholder('Ví dụ: Chị Nguyễn Thị Hà')
                                                            ->helperText('Tên hiển thị của phụ huynh'),
                                                    ]),

                                                Select::make('rating')
                                                    ->label('⭐ Số sao đánh giá')
                                                    ->options([
                                                        5 => '⭐⭐⭐⭐⭐ (5 sao)',
                                                        4.5 => '⭐⭐⭐⭐⭐ (4.5 sao)',
                                                        4 => '⭐⭐⭐⭐ (4 sao)',
                                                        3.5 => '⭐⭐⭐⭐ (3.5 sao)',
                                                        3 => '⭐⭐⭐ (3 sao)',
                                                    ])
                                                    ->default(5)
                                                    ->required(),

                                                Textarea::make('review_content')
                                                    ->label('💬 Nội dung đánh giá')
                                                    ->required()
                                                    ->rows(4)
                                                    ->placeholder('Ví dụ: "Tôi rất hài lòng với tài liệu ôn thi vào lớp 1 của LT365..."')
                                                    ->helperText('Nội dung đánh giá chi tiết của phụ huynh'),
                                            ])
                                            ->default([])
                                            ->addActionLabel('➕ Thêm đánh giá')
                                            ->collapsible()
                                            ->cloneable()
                                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Đánh giá mới')
                                            ->reorderableWithButtons()
                                            ->helperText('Khuyến nghị 3-5 đánh giá để hiển thị tốt nhất'),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\HomeSettingsResource\Pages\ManageHomeSettings::route('/'),
        ];
    }
}
