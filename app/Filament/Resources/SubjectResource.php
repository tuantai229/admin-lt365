<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubjectResource\Pages;
use App\Models\Subject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Nội dung phụ';

    protected static ?string $navigationLabel = 'Môn học';

    protected static ?string $modelLabel = 'Môn học';

    protected static ?string $pluralModelLabel = 'Môn học';

    protected static ?int $navigationSort = 2;

    // Kiểm tra permission
    public static function shouldRegisterNavigation(): bool
    {
        return auth('admin')->user()->can('view_any_subjects');
    }

    public static function canViewAny(): bool
    {
        return auth('admin')->user()->can('view_any_subjects');
    }

    public static function canCreate(): bool
    {
        return auth('admin')->user()->can('create_subjects');
    }

    public static function canEdit($record): bool
    {
        return auth('admin')->user()->can('update_subjects');
    }

    public static function canDelete($record): bool
    {
        return auth('admin')->user()->can('delete_subjects');
    }

    public static function canView($record): bool
    {
        return auth('admin')->user()->can('view_subjects');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin môn học')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Tên môn học')
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
                            ->unique(Subject::class, 'slug', ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('URL thân thiện. Ví dụ: toan-hoc')
                            ->rules(['regex:/^[a-z0-9-]+$/'])
                            ->validationMessages([
                                'regex' => 'Slug chỉ được chứa chữ cái thường, số và dấu gạch ngang',
                            ]),

                        Forms\Components\Select::make('status')
                            ->label('Trạng thái')
                            ->options([
                                0 => 'Không hoạt động',
                                1 => 'Hoạt động',
                            ])
                            ->default(0)
                            ->required(),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('Thứ tự sắp xếp')
                            ->numeric()
                            ->default(9999)
                            ->required()
                            ->helperText('Số càng nhỏ càng được hiển thị trước'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên môn học')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Đã sao chép!')
                    ->copyMessageDuration(1500),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Trạng thái')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        '0' => 'Không hoạt động',
                        '1' => 'Hoạt động',
                        default => 'Không xác định',
                    })
                    ->colors([
                        'danger' => '0',
                        'success' => '1',
                    ]),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Thứ tự')
                    ->sortable()
                    ->alignCenter(),

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
            'index' => Pages\ListSubjects::route('/'),
            'create' => Pages\CreateSubject::route('/create'),
            'edit' => Pages\EditSubject::route('/{record}/edit'),
        ];
    }
}
