<?php

namespace App\Filament\Resources;

use App\Filament\Forms\Components\SEOSection;
use App\Filament\Resources\CenterResource\Pages;
use App\Models\Center;
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

class CenterResource extends Resource
{
    protected static ?string $model = Center::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Giáo dục';

    protected static ?string $navigationLabel = 'Trung tâm';

    protected static ?string $modelLabel = 'Trung tâm';

    protected static ?string $pluralModelLabel = 'Trung tâm';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin cơ bản')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Tên trung tâm')
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
                            ->unique(Center::class, 'slug', ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('URL thân thiện. Ví dụ: trung-tam-anh-ngu-abc')
                            ->rules(['regex:/^[a-z0-9-]+$/'])
                            ->validationMessages([
                                'regex' => 'Slug chỉ được chứa chữ cái thường, số và dấu gạch ngang',
                            ]),

                        Forms\Components\TextInput::make('tagline')
                            ->label('Slogan')
                            ->maxLength(255)
                            ->helperText('Slogan ngắn gọn của trung tâm'),

                        Forms\Components\TextInput::make('experience')
                            ->label('Kinh nghiệm (năm)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->helperText('Số năm kinh nghiệm hoạt động'),

                        Forms\Components\Select::make('levels')
                            ->label('Cấp học')
                            ->relationship('levels', 'name')
                            ->options(Level::active()->ordered()->pluck('name', 'id'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Chọn các cấp học mà trung tâm giảng dạy'),

                        Forms\Components\Select::make('subjects')
                            ->label('Môn học')
                            ->relationship('subjects', 'name')
                            ->options(Subject::active()->ordered()->pluck('name', 'id'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Chọn các môn học mà trung tâm giảng dạy'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Thông tin liên hệ')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->label('Địa chỉ')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Địa chỉ chi tiết của trung tâm'),

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
                            ->afterStateUpdated(function (Set $set) {
                                $set('commune_id', 0);
                            })
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
                            ->label('Website')
                            ->url()
                            ->maxLength(255)
                            ->helperText('Website của trung tâm'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Hình ảnh và nội dung')
                    ->schema([
                        CuratorPicker::make('featured_image_id')
                            ->label('Ảnh đại diện')
                            ->buttonLabel('Chọn ảnh')
                            ->color('primary')
                            ->outlined(false)
                            ->size('sm')
                            ->helperText('Chọn ảnh đại diện cho trung tâm'),

                        TiptapEditor::make('content')
                            ->label('Mô tả chi tiết')
                            ->columnSpanFull()
                            ->profile('default')
                            ->tools([
                                'heading',
                                'bullet-list',
                                'ordered-list',
                                'checked-list',
                                'blockquote',
                                'hr',
                                '|',
                                'bold',
                                'italic',
                                'strike',
                                'underline',
                                'superscript',
                                'subscript',
                                'align-left',
                                'align-center',
                                'align-right',
                                '|',
                                'link',
                                'media',
                                'undo',
                                'redo',
                            ])
                            ->helperText('Mô tả chi tiết về trung tâm, cơ sở vật chất, đội ngũ giáo viên...'),
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
                            ->helperText('Trạng thái hiển thị của trung tâm'),
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
                    ->label('Tên trung tâm')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 40) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('tagline')
                    ->label('Slogan')
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),

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

                Tables\Columns\TextColumn::make('commune.name')
                    ->label('Quận/Huyện')
                    ->default('Toàn tỉnh/thành')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Điện thoại')
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
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
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        0 => 'Chưa duyệt',
                        1 => 'Đã duyệt',
                        2 => 'Đã ẩn',
                    ])
                    ->placeholder('Tất cả trạng thái'),

                Tables\Filters\SelectFilter::make('province_id')
                    ->label('Tỉnh/Thành phố')
                    ->options(Province::where('status', 1)->orderBy('name')->pluck('name', 'id'))
                    ->placeholder('Tất cả tỉnh/thành'),

                Tables\Filters\SelectFilter::make('levels')
                    ->label('Cấp học')
                    ->relationship('levels', 'name')
                    ->options(Level::active()->ordered()->pluck('name', 'id'))
                    ->placeholder('Tất cả cấp học'),

                Tables\Filters\SelectFilter::make('subjects')
                    ->label('Môn học')
                    ->relationship('subjects', 'name')
                    ->options(Subject::active()->ordered()->pluck('name', 'id'))
                    ->placeholder('Tất cả môn học'),

                Tables\Filters\Filter::make('experience')
                    ->label('Kinh nghiệm')
                    ->form([
                        Forms\Components\TextInput::make('experience_from')
                            ->label('Từ (năm)')
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\TextInput::make('experience_to')
                            ->label('Đến (năm)')
                            ->numeric()
                            ->minValue(0),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['experience_from'],
                                fn (Builder $query, $value): Builder => $query->where('experience', '>=', $value),
                            )
                            ->when(
                                $data['experience_to'],
                                fn (Builder $query, $value): Builder => $query->where('experience', '<=', $value),
                            );
                    }),

                Tables\Filters\Filter::make('created_at')
                    ->label('Ngày tạo')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Từ ngày'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Đến ngày'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('approve')
                        ->label('Duyệt')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 1]);
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('pending')
                        ->label('Chờ duyệt')
                        ->icon('heroicon-o-clock')
                        ->color('warning')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 0]);
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('hide')
                        ->label('Ẩn')
                        ->icon('heroicon-o-eye-slash')
                        ->color('danger')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 2]);
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
            'index' => Pages\ListCenters::route('/'),
            'create' => Pages\CreateCenter::route('/create'),
            'edit' => Pages\EditCenter::route('/{record}/edit'),
        ];
    }
}
