<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentTypeResource\Pages;
use App\Models\DocumentType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DocumentTypeResource extends Resource
{
    protected static ?string $model = DocumentType::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Nội dung phụ';

    protected static ?string $navigationLabel = 'Loại tài liệu';

    protected static ?string $modelLabel = 'Loại tài liệu';

    protected static ?string $pluralModelLabel = 'Loại tài liệu';

    protected static ?int $navigationSort = 3;

    // Kiểm tra permission
    public static function shouldRegisterNavigation(): bool
    {
        return auth('admin')->user()->can('view_any_document_types');
    }

    public static function canViewAny(): bool
    {
        return auth('admin')->user()->can('view_any_document_types');
    }

    public static function canCreate(): bool
    {
        return auth('admin')->user()->can('create_document_types');
    }

    public static function canEdit($record): bool
    {
        return auth('admin')->user()->can('update_document_types');
    }

    public static function canDelete($record): bool
    {
        return auth('admin')->user()->can('delete_document_types');
    }

    public static function canView($record): bool
    {
        return auth('admin')->user()->can('view_document_types');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin loại tài liệu')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Tên loại tài liệu')
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
                            ->unique(DocumentType::class, 'slug', ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('URL thân thiện. Ví dụ: de-thi, tai-lieu-on-tap, bai-tap'),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('Thứ tự hiển thị')
                            ->numeric()
                            ->default(9999)
                            ->helperText('Số thứ tự hiển thị. Số càng nhỏ càng hiển thị trước.'),

                        Forms\Components\Toggle::make('status')
                            ->label('Trạng thái hoạt động')
                            ->default(false)
                            ->helperText('Bật/tắt để hiển thị loại tài liệu này trên website.'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên loại tài liệu')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Đã copy!')
                    ->fontFamily('mono')
                    ->size('sm')
                    ->color('gray'),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Thứ tự')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\IconColumn::make('status')
                    ->label('Trạng thái')
                    ->boolean()
                    ->sortable()
                    ->alignCenter()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                // Tables\Columns\TextColumn::make('documents_count')
                //     ->label('Số tài liệu')
                //     ->counts('documents')
                //     ->alignCenter()
                //     ->badge()
                //     ->color('primary'),
                // TODO: Uncomment above when Document model is created

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
                        0 => 'Không hoạt động',
                        1 => 'Hoạt động',
                    ])
                    ->placeholder('Tất cả trạng thái'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListDocumentTypes::route('/'),
            'create' => Pages\CreateDocumentType::route('/create'),
            'edit' => Pages\EditDocumentType::route('/{record}/edit'),
        ];
    }
}
