<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LevelResource\Pages;
use App\Models\Level;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LevelResource extends Resource
{
    protected static ?string $model = Level::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Quản lý nội dung';

    protected static ?string $navigationLabel = 'Cấp học';

    protected static ?string $modelLabel = 'Cấp học';

    protected static ?string $pluralModelLabel = 'Cấp học';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin cấp học')
                    ->schema([
                        Forms\Components\Select::make('parent_id')
                            ->label('Cấp học cha')
                            ->options(Level::active()->ordered()->pluck('name', 'id'))
                            ->placeholder('Chọn cấp học cha (nếu có)')
                            ->searchable()
                            ->nullable(),

                        Forms\Components\TextInput::make('name')
                            ->label('Tên cấp học')
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
                            ->unique(Level::class, 'slug', ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('URL thân thiện. Ví dụ: mam-non')
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
                    ->label('Tên cấp học')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($record, $state) {
                        if ($record->parent_id) {
                            return new \Illuminate\Support\HtmlString('&nbsp;&nbsp;&nbsp;&nbsp;🔸 ' . $state);
                        }
                        return new \Illuminate\Support\HtmlString('📁 ' . $state);
                    }),

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
                        '1' => 'Hoạt động',
                        '0' => 'Không hoạt động',
                    ]),

                Tables\Filters\SelectFilter::make('parent_id')
                    ->label('Cấp học cha')
                    ->options(Level::active()->ordered()->pluck('name', 'id'))
                    ->placeholder('Tất cả'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc')
            ->modifyQueryUsing(function (Builder $query) {
                // Sắp xếp để hiển thị parent và children liền nhau
                return $query->selectRaw('
                        levels.*,
                        CASE 
                            WHEN parent_id = 0 THEN sort_order 
                            ELSE (SELECT sort_order FROM levels parent WHERE parent.id = levels.parent_id) 
                        END as parent_sort_order
                    ')
                    ->orderByRaw('parent_sort_order, parent_id = 0 DESC, sort_order');
            });
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
            'index' => Pages\ListLevels::route('/'),
            'create' => Pages\CreateLevel::route('/create'),
            'edit' => Pages\EditLevel::route('/{record}/edit'),
        ];
    }
}