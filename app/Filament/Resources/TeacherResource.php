<?php

namespace App\Filament\Resources;

use App\Filament\Forms\Components\SEOSection;
use App\Filament\Resources\TeacherResource\Pages;
use App\Models\Teacher;
use App\Models\Province;
use App\Models\Commune;
use App\Models\Level;
use App\Models\Subject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use FilamentTiptapEditor\TiptapEditor;
use Filament\Forms\Get;
use Filament\Forms\Set;

class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Giáo dục';

    protected static ?string $navigationLabel = 'Giáo viên';

    protected static ?string $modelLabel = 'Giáo viên';

    protected static ?string $pluralModelLabel = 'Giáo viên';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin cơ bản')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Tên giáo viên')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $context, $state, callable $set) {
                                if ($context === 'create') {
                                    $set('slug', Str::slug($state));
                                }
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(Teacher::class, 'slug', ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('URL thân thiện. Ví dụ: co-giao-nguyen-thi-a')
                            ->rules(['regex:/^[a-z0-9-]+$/'])
                            ->validationMessages([
                                'regex' => 'Slug chỉ được chứa chữ cái thường, số và dấu gạch ngang',
                            ]),

                        Forms\Components\TextInput::make('tagline')
                            ->label('Chức danh/Slogan')
                            ->maxLength(255)
                            ->helperText('Slogan ngắn gọn hoặc chức danh của giáo viên'),

                        Forms\Components\TextInput::make('experience')
                            ->label('Kinh nghiệm (năm)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->helperText('Số năm kinh nghiệm giảng dạy'),

                        Forms\Components\Select::make('levels')
                            ->label('Cấp học giảng dạy')
                            ->relationship('levels', 'name')
                            ->options(Level::active()->ordered()->pluck('name', 'id'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Chọn các cấp học mà giáo viên giảng dạy'),

                        Forms\Components\Select::make('subjects')
                            ->label('Môn học giảng dạy')
                            ->relationship('subjects', 'name')
                            ->options(Subject::active()->ordered()->pluck('name', 'id'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Chọn các môn học mà giáo viên giảng dạy'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Thông tin liên hệ và địa chỉ')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->label('Địa chỉ')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Địa chỉ chi tiết của giáo viên'),

                        Forms\Components\Select::make('province_id')
                            ->label('Tỉnh/Thành phố')
                            ->options(function () {
                                $provinces = Province::where('status', 1)->orderBy('name')->pluck('name', 'id');
                                return [0 => 'Toàn quốc'] + $provinces->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('commune_id', 0))
                            ->helperText('Chọn tỉnh/thành phố hoặc để "Toàn quốc"'),

                        Forms\Components\Select::make('commune_id')
                            ->label('Quận/Huyện/Xã')
                            ->options(function (Get $get) {
                                $provinceId = $get('province_id');
                                if (!$provinceId || $provinceId == 0) {
                                    return [0 => 'Toàn tỉnh/thành'];
                                }
                                $communes = Commune::where('province_id', $provinceId)
                                    ->where('status', 1)
                                    ->orderBy('name')
                                    ->pluck('name', 'id');
                                return [0 => 'Toàn tỉnh/thành'] + $communes->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->default(0)
                            ->helperText('Chọn quận/huyện/xã hoặc để "Toàn tỉnh/thành"'),

                        Forms\Components\TextInput::make('phone')
                            ->label('Số điện thoại')
                            ->tel()
                            ->maxLength(20)
                            ->helperText('Số điện thoại liên hệ'),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255)
                            ->helperText('Email liên hệ'),

                        Forms\Components\TextInput::make('website')
                            ->label('Website/Profile')
                            ->url()
                            ->maxLength(255)
                            ->helperText('Website hoặc trang cá nhân của giáo viên'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Hình ảnh và giới thiệu')
                    ->schema([
                        CuratorPicker::make('featured_image_id')
                            ->label('Ảnh đại diện')
                            ->buttonLabel('Chọn ảnh')
                            ->color('primary')
                            ->outlined(false)
                            ->size('sm')
                            ->helperText('Chọn ảnh đại diện cho giáo viên'),

                        TiptapEditor::make('content')
                            ->label('Bài viết giới thiệu')
                            ->columnSpanFull()
                            ->profile('default')
                            ->helperText('Viết bài giới thiệu chi tiết về giáo viên, thành tích, phương pháp giảng dạy...'),
                    ])
                    ->columnSpanFull(),

                Forms\Components\Section::make('Cài đặt')
                    ->schema([
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Thứ tự sắp xếp')
                            ->numeric()
                            ->default(9999)
                            ->required()
                            ->helperText('Số càng nhỏ càng được hiển thị trước'),

                        Forms\Components\Select::make('status')
                            ->label('Trạng thái')
                            ->options([
                                0 => 'Chưa duyệt',
                                1 => 'Đã duyệt',
                                2 => 'Đã ẩn',
                            ])
                            ->default(0)
                            ->required()
                            ->helperText('Trạng thái hiển thị của giáo viên'),
                    ])
                    ->columns(2),
                
                SEOSection::make(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                CuratorColumn::make('featured_image_id')
                    ->label('Ảnh')
                    ->size(60)
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Tên giáo viên')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->tooltip(fn (string $state) => strlen($state) <= 40 ? null : $state),

                Tables\Columns\TextColumn::make('levels.name')
                    ->label('Cấp học')
                    ->badge()
                    ->color('info')
                    ->separator(',')
                    ->limit(2),

                Tables\Columns\TextColumn::make('subjects.name')
                    ->label('Môn học')
                    ->badge()
                    ->color('success')
                    ->separator(',')
                    ->limit(2),

                Tables\Columns\TextColumn::make('province.name')
                    ->label('Tỉnh/Thành')
                    ->default('Toàn quốc')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Điện thoại')
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('experience')
                    ->label('Kinh nghiệm')
                    ->suffix(' năm')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'gray',
                        '1' => 'success',
                        '2' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        '0' => 'Chưa duyệt',
                        '1' => 'Đã duyệt',
                        '2' => 'Đã ẩn',
                        default => 'Không xác định',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Thứ tự')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        0 => 'Chưa duyệt',
                        1 => 'Đã duyệt',
                        2 => 'Đã ẩn',
                    ]),
                Tables\Filters\SelectFilter::make('province_id')
                    ->label('Tỉnh/Thành phố')
                    ->options(Province::where('status', 1)->orderBy('name')->pluck('name', 'id'))
                    ->searchable(),
                Tables\Filters\SelectFilter::make('levels')
                    ->label('Cấp học')
                    ->relationship('levels', 'name')
                    ->searchable()
                    ->multiple(),
                Tables\Filters\SelectFilter::make('subjects')
                    ->label('Môn học')
                    ->relationship('subjects', 'name')
                    ->searchable()
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeachers::route('/'),
            'create' => Pages\CreateTeacher::route('/create'),
            'edit' => Pages\EditTeacher::route('/{record}/edit'),
        ];
    }
}
