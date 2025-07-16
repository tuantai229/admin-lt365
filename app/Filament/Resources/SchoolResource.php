<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SchoolResource\Pages;
use App\Filament\Resources\SchoolResource\RelationManagers;
use App\Models\School;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Commune;
use App\Models\Level;
use App\Models\Province;
use App\Models\SchoolType;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Awcodes\Curator\Components\Forms\CuratorPicker;

class SchoolResource extends Resource
{
    protected static ?string $model = School::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Quản lý nội dung';

    protected static ?string $navigationLabel = 'Trường học';

    protected static ?string $modelLabel = 'Trường học';

    protected static ?string $pluralModelLabel = 'Trường học';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Thông tin chung')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Tên trường')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $context, $state, callable $set) {
                                        if ($context === 'create') {
                                            $set('slug', Str::slug($state));
                                        }
                                    })
                                    ->placeholder('Ví dụ: Trường THPT Chuyên Khoa học Tự nhiên'),

                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->unique(School::class, 'slug', ignoreRecord: true)
                                    ->maxLength(255)
                                    ->helperText('URL thân thiện. Ví dụ: truong-thpt-chuyen-khoa-hoc-tu-nhien'),

                                Select::make('level_id')
                                    ->label('Cấp học')
                                    ->relationship('level', 'name')
                                    ->options(Level::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Select::make('schoolTypes')
                                    ->label('Loại trường')
                                    ->relationship('schoolTypes', 'name')
                                    ->options(SchoolType::all()->pluck('name', 'id'))
                                    ->multiple()
                                    ->preload()
                                    ->searchable(),

                                TextInput::make('address')
                                    ->label('Địa chỉ')
                                    ->maxLength(255)
                                    ->placeholder('Ví dụ: 19 Lê Thánh Tông, Hoàn Kiếm, Hà Nội'),

                                Select::make('province_id')
                                    ->label('Tỉnh/Thành phố')
                                    ->options(function () {
                                        $provinces = Province::all()->pluck('name', 'id')->toArray();
                                        return [0 => 'Toàn quốc'] + $provinces;
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->afterStateUpdated(fn (callable $set) => $set('commune_id', 0)),

                                Select::make('commune_id')
                                    ->label('Quận/Huyện/Xã/Phường')
                                    ->options(function (callable $get) {
                                        $provinceId = $get('province_id');
                                        if ($provinceId === null || $provinceId === 0) {
                                            return [0 => 'Toàn tỉnh/thành'];
                                        }
                                        $communes = Commune::where('province_id', $provinceId)->pluck('name', 'id')->toArray();
                                        return [0 => 'Toàn tỉnh/thành'] + $communes;
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->default(0),

                                TextInput::make('phone')
                                    ->label('Số điện thoại')
                                    ->tel()
                                    ->maxLength(20),

                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->maxLength(255),

                                TextInput::make('website')
                                    ->label('Website')
                                    ->url()
                                    ->maxLength(255),

                                Forms\Components\RichEditor::make('content')
                                    ->label('Nội dung giới thiệu')
                                    ->columnSpanFull(),

                                CuratorPicker::make('featured_image_id')
                                    ->label('Ảnh đại diện')
                                    ->nullable()
                                    ->columnSpan('full'),

                                TextInput::make('sort_order')
                                    ->label('Thứ tự hiển thị')
                                    ->numeric()
                                    ->default(9999)
                                    ->helperText('Số thứ tự hiển thị. Số càng nhỏ càng hiển thị trước.'),

                                Toggle::make('status')
                                    ->label('Trạng thái hoạt động')
                                    ->default(false)
                                    ->helperText('Bật/tắt để hiển thị trường này trên website.'),
                            ])
                            ->columns(2),

                        Forms\Components\Section::make('Thông tin tuyển sinh')
                            ->schema([
                                Repeater::make('admissions')
                                    ->relationship('admissions')
                                    ->label('Thông tin tuyển sinh theo năm')
                                    ->schema([
                                        TextInput::make('year')
                                            ->label('Năm học')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Ví dụ: 2025 hoặc 2025-2026'),
                                        TextInput::make('total_students')
                                            ->label('Tổng chỉ tiêu học sinh')
                                            ->numeric()
                                            ->nullable(),
                                        TextInput::make('number_of_classes')
                                            ->label('Số lớp dự kiến')
                                            ->numeric()
                                            ->nullable(),
                                        TextInput::make('students_per_class')
                                            ->label('Sĩ số mỗi lớp')
                                            ->numeric()
                                            ->nullable(),
                                        TextInput::make('estimated_tuition_fee')
                                            ->label('Học phí ước tính (VNĐ/tháng)')
                                            ->numeric()
                                            ->nullable(),
                                        TextInput::make('program_type')
                                            ->label('Kiểu chương trình học')
                                            ->maxLength(255)
                                            ->nullable()
                                            ->placeholder('Ví dụ: Chương trình chuẩn'),
                                        Forms\Components\DatePicker::make('register_start_date')
                                            ->label('Thời gian nộp hồ sơ (Bắt đầu)')
                                            ->nullable(),
                                        Forms\Components\DatePicker::make('register_end_date')
                                            ->label('Thời gian nộp hồ sơ (Kết thúc)')
                                            ->nullable(),
                                        Forms\Components\DatePicker::make('exam_date')
                                            ->label('Ngày thi tuyển')
                                            ->nullable(),
                                        Forms\Components\DatePicker::make('result_announcement_date')
                                            ->label('Ngày công bố kết quả')
                                            ->nullable(),
                                    ])
                                    ->columns(2)
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['year'] ?? null)
                                    ->defaultItems(0)
                                    ->addActionLabel('Thêm thông tin tuyển sinh'),
                            ]),

                        Forms\Components\Section::make('Phương thức tuyển sinh')
                            ->schema([
                                Repeater::make('admissionMethods')
                                    ->relationship('admissionMethods')
                                    ->label('Phương thức tuyển sinh')
                                    ->schema([
                                        TextInput::make('method_name')
                                            ->label('Tên phương thức')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Ví dụ: Thi Toán, Phỏng vấn'),
                                        TextInput::make('duration_minutes')
                                            ->label('Thời lượng (phút)')
                                            ->numeric()
                                            ->nullable(),
                                        TextInput::make('sort_order')
                                            ->label('Thứ tự hiển thị')
                                            ->numeric()
                                            ->default(9999),
                                    ])
                                    ->columns(2)
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['method_name'] ?? null)
                                    ->defaultItems(0)
                                    ->addActionLabel('Thêm phương thức tuyển sinh'),
                            ]),

                        Forms\Components\Section::make('Thống kê tuyển sinh')
                            ->schema([
                                Repeater::make('admissionStats')
                                    ->relationship('admissionStats')
                                    ->label('Thống kê tuyển sinh lịch sử')
                                    ->schema([
                                        TextInput::make('academic_year')
                                            ->label('Năm học')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Ví dụ: 2024-2025'),
                                        TextInput::make('target_quota')
                                            ->label('Chỉ tiêu tuyển sinh')
                                            ->numeric()
                                            ->nullable(),
                                        TextInput::make('registered_count')
                                            ->label('Số lượng hồ sơ đăng ký')
                                            ->numeric()
                                            ->nullable(),
                                        TextInput::make('cutoff_score')
                                            ->label('Điểm chuẩn')
                                            ->numeric()
                                            ->nullable(),
                                        TextInput::make('cutoff_score_max')
                                            ->label('Tổng điểm tối đa')
                                            ->numeric()
                                            ->nullable(),
                                        TextInput::make('sort_order')
                                            ->label('Thứ tự hiển thị')
                                            ->numeric()
                                            ->default(9999),
                                    ])
                                    ->columns(2)
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['academic_year'] ?? null)
                                    ->defaultItems(0)
                                    ->addActionLabel('Thêm thống kê tuyển sinh'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Thông tin khác')
                            ->schema([
                                // Add other fields here if needed
                            ])
                            ->columns(1),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên trường')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('level.name')
                    ->label('Cấp học')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('province.name')
                    ->label('Tỉnh/Thành phố')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('commune.name')
                    ->label('Quận/Huyện/Xã/Phường')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('status')
                    ->label('Trạng thái')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Thứ tự')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('level_id')
                    ->label('Cấp học')
                    ->relationship('level', 'name')
                    ->options(Level::all()->pluck('name', 'id'))
                    ->placeholder('Tất cả cấp học'),
                Tables\Filters\SelectFilter::make('province_id')
                    ->label('Tỉnh/Thành phố')
                    ->relationship('province', 'name')
                    ->options(Province::all()->pluck('name', 'id'))
                    ->placeholder('Tất cả tỉnh/thành'),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        0 => 'Không hoạt động',
                        1 => 'Hoạt động',
                    ])
                    ->placeholder('Tất cả trạng thái'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Kích hoạt')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 1]);
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Vô hiệu hóa')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 0]);
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
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
            'index' => Pages\ListSchools::route('/'),
            'create' => Pages\CreateSchool::route('/create'),
            'edit' => Pages\EditSchool::route('/{record}/edit'),
        ];
    }

    protected static function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['commune_id'])) {
            $data['commune_id'] = 0;
        }
        return $data;
    }

    protected static function mutateFormDataBeforeSave(array $data): array
    {
        if (empty($data['commune_id'])) {
            $data['commune_id'] = 0;
        }
        return $data;
    }
}
