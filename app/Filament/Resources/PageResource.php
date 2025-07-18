<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use FilamentTiptapEditor\TiptapEditor;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Nội dung phụ';

    protected static ?string $navigationLabel = 'Trang đơn';

    protected static ?string $modelLabel = 'Trang đơn';

    protected static ?string $pluralModelLabel = 'Trang đơn';

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin cơ bản')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Tên trang')
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
                            ->unique(Page::class, 'slug', ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('URL thân thiện. Ví dụ: chinh-sach-bao-mat')
                            ->rules(['regex:/^[a-z0-9-]+$/'])
                            ->validationMessages([
                                'regex' => 'Slug chỉ được chứa chữ cái thường, số và dấu gạch ngang',
                            ]),

                        Forms\Components\Select::make('status')
                            ->label('Trạng thái')
                            ->options([
                                0 => 'Nháp',
                                1 => 'Đã xuất bản',
                                2 => 'Đã ẩn',
                            ])
                            ->default(0)
                            ->required()
                            ->helperText('Trạng thái hiển thị của trang'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Nội dung trang')
                    ->schema([
                        TiptapEditor::make('content')
                            ->label('Nội dung')
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
                            ->helperText('Nhập nội dung chi tiết của trang'),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên trang')
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

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),

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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
