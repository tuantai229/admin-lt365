<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Models\Document;
use App\Models\Level;
use App\Models\Subject;
use App\Models\DocumentType;
use App\Models\DifficultyLevel;
use App\Models\School;
use App\Models\Tag;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use FilamentTiptapEditor\TiptapEditor;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Quản lý nội dung';

    protected static ?string $navigationLabel = 'Tài liệu';

    protected static ?string $modelLabel = 'Tài liệu';

    protected static ?string $pluralModelLabel = 'Tài liệu';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Thông tin cơ bản')
                            ->schema([
                                Forms\Components\Section::make('Thông tin tài liệu')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Tên tài liệu')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (string $context, $state, callable $set) {
                                                if ($context === 'create') {
                                                    $set('slug', \Illuminate\Support\Str::slug($state));
                                                }
                                            }),

                                        Forms\Components\TextInput::make('slug')
                                            ->label('Slug')
                                            ->required()
                                            ->unique(Document::class, 'slug', ignoreRecord: true)
                                            ->maxLength(255)
                                            ->helperText('URL thân thiện cho tài liệu này'),

                                        Forms\Components\Textarea::make('description')
                                            ->label('Mô tả ngắn')
                                            ->rows(3)
                                            ->maxLength(500)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('Ảnh đại diện')
                                    ->schema([
                                        CuratorPicker::make('featured_image_id')
                                            ->label('Ảnh đại diện')
                                            ->buttonLabel('Chọn ảnh')
                                            ->color('primary')
                                            ->outlined(false)
                                            ->size('sm')
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->helperText('Chọn ảnh đại diện cho tài liệu'),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Phân loại')
                            ->schema([
                                Forms\Components\Section::make('Thông tin phân loại')
                                    ->schema([
                                        Forms\Components\Select::make('level_id')
                                            ->label('Cấp học')
                                            ->options(Level::active()->ordered()->pluck('name', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Chọn cấp học')
                                            ->helperText('Để trống nếu không thuộc cấp học cụ thể'),

                                        Forms\Components\Select::make('subject_id')
                                            ->label('Môn học')
                                            ->options(Subject::active()->ordered()->pluck('name', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Chọn môn học')
                                            ->helperText('Để trống nếu không thuộc môn học cụ thể'),

                                        Forms\Components\Select::make('document_type_id')
                                            ->label('Loại tài liệu')
                                            ->options(DocumentType::active()->ordered()->pluck('name', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Chọn loại tài liệu')
                                            ->helperText('Ví dụ: Đề thi, Tài liệu ôn tập, Bài tập...'),

                                        Forms\Components\Select::make('difficulty_level_id')
                                            ->label('Mức độ khó')
                                            ->options(DifficultyLevel::active()->ordered()->pluck('name', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Chọn mức độ khó')
                                            ->helperText('Để trống nếu không phân loại theo độ khó'),

                                        Forms\Components\Select::make('school_id')
                                            ->label('Trường học')
                                            ->options(School::active()->orderBy('name')->pluck('name', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Chọn trường học')
                                            ->default(0)
                                            ->helperText('Để trống nếu không thuộc trường học cụ thể'),

                                        Forms\Components\TextInput::make('year')
                                            ->label('Năm học')
                                            ->numeric()
                                            ->placeholder('2024')
                                            ->default(0)
                                            ->helperText('Năm học hoặc năm thi'),
                                    ])
                                    ->columns(2),

                                Forms\Components\Section::make('Tags')
                                    ->schema([
                                        Forms\Components\Select::make('tags')
                                            ->label('Tags')
                                            ->multiple()
                                            ->relationship('tags', 'name')
                                            ->options(Tag::where('status', 1)->pluck('name', 'id'))
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
                                                            $set('slug', \Illuminate\Support\Str::slug($state));
                                                        }
                                                    }),
                                                Forms\Components\TextInput::make('slug')
                                                    ->label('Slug')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\Hidden::make('status')
                                                    ->default(1),
                                            ])
                                            ->helperText('Chọn hoặc tạo mới các tag cho tài liệu'),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('File & Giá')
                            ->schema([
                                Forms\Components\Section::make('Quản lý file')
                                    ->schema([
                                        Forms\Components\FileUpload::make('file_path')
                                            ->label('File tài liệu')
                                            ->disk('public')
                                            ->directory('documents')
                                            ->visibility('private')
                                            ->acceptedFileTypes([
                                                'application/pdf',
                                                'application/msword',
                                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                                'application/vnd.ms-excel',
                                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                                'application/vnd.ms-powerpoint',
                                                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                                                'image/jpeg',
                                                'image/png',
                                                'image/gif',
                                                'text/plain',
                                            ])
                                            ->maxSize(51200) // 50MB
                                            ->uploadingMessage('Đang tải file...')
                                            ->helperText('Chọn file tài liệu. Tối đa 50MB. Hỗ trợ: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, PNG, GIF, TXT')
                                            ->columnSpanFull()
                                            ->preserveFilenames()
                                            ->downloadable()
                                            ->previewable(false),

                                        Forms\Components\Placeholder::make('file_info')
                                            ->label('Thông tin file')
                                            ->content(function ($record) {
                                                if (!$record || !$record->file_path) {
                                                    return 'Chưa có file';
                                                }
                                                
                                                return view('filament.placeholders.file-info', [
                                                    'fileSize' => $record->formatted_file_size,
                                                    'fileType' => $record->file_type,
                                                    'downloadCount' => $record->download_count,
                                                ]);
                                            })
                                            ->columnSpanFull(),
                                    ]),

                                Forms\Components\Section::make('Thông tin giá & trạng thái')
                                    ->schema([
                                        Forms\Components\TextInput::make('price')
                                            ->label('Giá (VND)')
                                            ->numeric()
                                            ->default(0)
                                            ->helperText('Nhập 0 cho tài liệu miễn phí')
                                            ->suffix('VND'),

                                        Forms\Components\Toggle::make('is_featured')
                                            ->label('Tài liệu nổi bật')
                                            ->default(false)
                                            ->helperText('Hiển thị trong danh sách tài liệu nổi bật'),

                                        Forms\Components\Select::make('status')
                                            ->label('Trạng thái')
                                            ->options([
                                                0 => 'Nháp',
                                                1 => 'Đã xuất bản',
                                                2 => 'Ẩn',
                                            ])
                                            ->default(0)
                                            ->required()
                                            ->helperText('Chỉ tài liệu đã xuất bản mới hiển thị trên website'),

                                        Forms\Components\TextInput::make('sort_order')
                                            ->label('Thứ tự hiển thị')
                                            ->numeric()
                                            ->default(9999)
                                            ->helperText('Số càng nhỏ càng hiển thị trước'),
                                    ])
                                    ->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Nội dung')
                            ->schema([
                                TiptapEditor::make('content')
                                    ->label('Nội dung chi tiết')
                                    ->profile('default')
                                    ->columnSpanFull()
                                    ->helperText('Mô tả chi tiết về tài liệu, hướng dẫn sử dụng, v.v...'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featuredImage.path')
                    ->label('Ảnh')
                    ->disk('public')
                    ->height(50)
                    ->width(50)
                    ->defaultImageUrl(url('/images/placeholder.png')),

                Tables\Columns\TextColumn::make('name')
                    ->label('Tên tài liệu')
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

                Tables\Columns\TextColumn::make('level.name')
                    ->label('Cấp học')
                    ->sortable()
                    ->searchable()
                    ->placeholder('--'),

                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Môn học')
                    ->sortable()
                    ->searchable()
                    ->placeholder('--'),

                Tables\Columns\TextColumn::make('documentType.name')
                    ->label('Loại')
                    ->sortable()
                    ->searchable()
                    ->placeholder('--'),

                Tables\Columns\TextColumn::make('formatted_price')
                    ->label('Giá')
                    ->sortable(['price'])
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Miễn phí' ? 'success' : 'primary'),

                Tables\Columns\TextColumn::make('download_count')
                    ->label('Lượt tải')
                    ->sortable()
                    ->numeric(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Nổi bật')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Trạng thái')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        '0' => 'Nháp',
                        '1' => 'Đã xuất bản',
                        '2' => 'Ẩn',
                        default => 'Không xác định',
                    })
                    ->colors([
                        'secondary' => '0',
                        'success' => '1',
                        'danger' => '2',
                    ]),

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
                    ->options(Level::active()->ordered()->pluck('name', 'id'))
                    ->placeholder('Tất cả cấp học'),

                Tables\Filters\SelectFilter::make('subject_id')
                    ->label('Môn học')
                    ->options(Subject::active()->ordered()->pluck('name', 'id'))
                    ->placeholder('Tất cả môn học'),

                Tables\Filters\SelectFilter::make('document_type_id')
                    ->label('Loại tài liệu')
                    ->options(DocumentType::active()->ordered()->pluck('name', 'id'))
                    ->placeholder('Tất cả loại'),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        0 => 'Nháp',
                        1 => 'Đã xuất bản',
                        2 => 'Ẩn',
                    ])
                    ->placeholder('Tất cả trạng thái'),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Tài liệu nổi bật')
                    ->placeholder('Tất cả')
                    ->trueLabel('Nổi bật')
                    ->falseLabel('Không nổi bật'),

                Tables\Filters\Filter::make('price_filter')
                    ->label('Loại giá')
                    ->form([
                        Forms\Components\Select::make('price_type')
                            ->label('Loại')
                            ->options([
                                'free' => 'Miễn phí',
                                'paid' => 'Có phí',
                            ])
                            ->placeholder('Tất cả'),
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['price_type'] === 'free') {
                            return $query->where('price', 0);
                        } elseif ($data['price_type'] === 'paid') {
                            return $query->where('price', '>', 0);
                        }
                        return $query;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('download')
                    ->label('Tải về')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn (Document $record): string => route('documents.download', $record))
                    ->openUrlInNewTab()
                    ->visible(fn (Document $record): bool => $record->hasFile()),
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
                    Tables\Actions\BulkAction::make('featured')
                        ->label('Đặt nổi bật')
                        ->icon('heroicon-o-star')
                        ->color('warning')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['is_featured' => true]);
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
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
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
            'view' => Pages\ViewDocument::route('/{record}'),
        ];
    }
}