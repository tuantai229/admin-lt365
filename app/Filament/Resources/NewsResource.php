<?php

namespace App\Filament\Resources;

use App\Filament\Forms\Components\SEOSection;
use App\Filament\Resources\NewsResource\Pages;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\School;
use App\Models\Tag;
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

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Nội dung chính';

    protected static ?string $navigationLabel = 'Tin tức';

    protected static ?string $modelLabel = 'Tin tức';

    protected static ?string $pluralModelLabel = 'Tin tức';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin cơ bản')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Tiêu đề tin tức')
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
                            ->unique(News::class, 'slug', ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('URL thân thiện. Ví dụ: tin-tuc-giao-duc-moi-nhat')
                            ->rules(['regex:/^[a-z0-9-]+$/'])
                            ->validationMessages([
                                'regex' => 'Slug chỉ được chứa chữ cái thường, số và dấu gạch ngang',
                            ]),

                        Forms\Components\Select::make('school_id')
                            ->label('Trường học')
                            ->options(School::active()->orderBy('name')->pluck('name', 'id'))
                            ->placeholder('Chọn trường học (để trống nếu là tin tức chung)')
                            ->searchable()
                            ->preload()
                            ->default(0),

                        Forms\Components\Hidden::make('admin_user_id')
                            ->default(fn() => auth()->id()),

                        Forms\Components\Select::make('categories')
                            ->label('Danh mục tin tức')
                            ->relationship('categories', 'name')
                            ->options(NewsCategory::active()->orderBy('name')->pluck('name', 'id'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Chọn một hoặc nhiều danh mục cho tin tức'),

                        Forms\Components\Select::make('tags')
                            ->label('Tags')
                            ->relationship('tags', 'name')
                            ->options(Tag::where('status', 1)->orderBy('name')->pluck('name', 'id'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Tên tag')
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
                                    ->unique(Tag::class, 'slug')
                                    ->maxLength(255),
                                Forms\Components\Toggle::make('status')
                                    ->label('Trạng thái hoạt động')
                                    ->default(true),
                            ])
                            ->helperText('Chọn tags có sẵn hoặc tạo mới'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Nội dung')
                    ->schema([
                        CuratorPicker::make('featured_image_id')
                            ->label('Ảnh đại diện')
                            ->buttonLabel('Chọn ảnh')
                            ->color('primary')
                            ->outlined(false)
                            ->size('sm')
                            ->helperText('Chọn ảnh đại diện cho tin tức'),

                        TiptapEditor::make('content')
                            ->label('Nội dung tin tức')
                            ->required()
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
                            ->helperText('Nhập nội dung chi tiết của tin tức'),
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

                        Forms\Components\Toggle::make('is_featured')
                            ->label('Tin tức nổi bật')
                            ->default(false)
                            ->helperText('Đánh dấu tin tức này là nổi bật'),

                        Forms\Components\Select::make('status')
                            ->label('Trạng thái')
                            ->options([
                                0 => 'Nháp',
                                1 => 'Đã xuất bản',
                                2 => 'Đã ẩn',
                            ])
                            ->default(0)
                            ->required()
                            ->helperText('Trạng thái hiển thị của tin tức'),

                        Forms\Components\TextInput::make('view_count')
                            ->label('Lượt xem')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->helperText('Số lượt xem tin tức (tự động cập nhật)'),
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
                    ->label('Tiêu đề')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Danh mục')
                    ->badge()
                    ->color('info')
                    ->separator(','),

                Tables\Columns\TextColumn::make('school.name')
                    ->label('Trường học')
                    ->default('Tin tức chung')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('adminUser.name')
                    ->label('Người tạo')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('view_count')
                    ->label('Lượt xem')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('success'),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Nổi bật')
                    ->boolean()
                    ->sortable()
                    ->alignCenter()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),

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
                        '0' => 'Nháp',
                        '1' => 'Đã xuất bản',
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
                        0 => 'Nháp',
                        1 => 'Đã xuất bản',
                        2 => 'Đã ẩn',
                    ])
                    ->placeholder('Tất cả trạng thái'),

                Tables\Filters\Filter::make('is_featured')
                    ->label('Tin tức nổi bật')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->where('is_featured', true)),

                Tables\Filters\SelectFilter::make('school_id')
                    ->label('Trường học')
                    ->options(School::active()->orderBy('name')->pluck('name', 'id'))
                    ->placeholder('Tất cả trường học'),

                Tables\Filters\SelectFilter::make('categories')
                    ->label('Danh mục')
                    ->relationship('categories', 'name')
                    ->options(NewsCategory::active()->orderBy('name')->pluck('name', 'id'))
                    ->placeholder('Tất cả danh mục'),

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
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Xuất bản')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 1]);
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('draft')
                        ->label('Chuyển thành nháp')
                        ->icon('heroicon-o-pencil')
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
                    Tables\Actions\BulkAction::make('toggle_featured')
                        ->label('Đánh dấu nổi bật')
                        ->icon('heroicon-o-star')
                        ->color('warning')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['is_featured' => !$record->is_featured]);
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc')
            ->defaultSort('created_at', 'desc')
            ->defaultSort('id', 'desc')
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
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}
