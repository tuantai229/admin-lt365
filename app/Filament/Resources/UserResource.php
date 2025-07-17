<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use App\Helpers\FormatHelper;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Quản lý người dùng';
    protected static ?string $navigationLabel = 'Tài khoản người dùng';
    protected static ?string $modelLabel = 'Tài khoản người dùng';
    protected static ?string $pluralModelLabel = 'Tài khoản người dùng';
    protected static ?int $navigationSort = 1;

    // Kiểm tra permission
    public static function shouldRegisterNavigation(): bool
    {
        return auth('admin')->user()->can('view_any_users');
    }

    public static function canViewAny(): bool
    {
        return auth('admin')->user()->can('view_any_users');
    }

    public static function canCreate(): bool
    {
        return auth('admin')->user()->can('create_users');
    }

    public static function canEdit($record): bool
    {
        return auth('admin')->user()->can('update_users');
    }

    public static function canDelete($record): bool
    {
        return auth('admin')->user()->can('delete_users');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Thông tin cơ bản')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('full_name')
                                ->label('Họ và tên')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(255),
                        ]),
                    Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('phone')
                                ->label('Số điện thoại')
                                ->tel()
                                ->maxLength(15),
                            Forms\Components\Select::make('gender')
                                ->label('Giới tính')
                                ->options([
                                    'male' => 'Nam',
                                    'female' => 'Nữ',
                                    'other' => 'Khác',
                                ])
                                ->placeholder('Chọn giới tính'),
                        ]),
                    Grid::make(2)
                        ->schema([
                            Forms\Components\DatePicker::make('date_of_birth')
                                ->label('Ngày sinh')
                                ->maxDate(now()),
                            Forms\Components\Toggle::make('status')
                                ->label('Trạng thái hoạt động')
                                ->default(true),
                        ]),
                ])->columns(1),

            Section::make('Thông tin liên hệ')
                ->schema([
                    Forms\Components\Textarea::make('address')
                        ->label('Địa chỉ')
                        ->rows(2)
                        ->maxLength(500),
                    Forms\Components\Textarea::make('bio')
                        ->label('Giới thiệu bản thân')
                        ->rows(3)
                        ->maxLength(1000),
                ])->columns(1),

            Section::make('Thông tin tài khoản')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('password')
                                ->label('Mật khẩu')
                                ->password()
                                ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                ->dehydrated(fn ($state) => filled($state))
                                ->required(fn (string $context): bool => $context === 'create')
                                ->hiddenOn('view'),
                            Forms\Components\FileUpload::make('avatar')
                                ->label('Ảnh đại diện')
                                ->image()
                                ->directory('avatars')
                                ->visibility('public')
                                ->imageEditor()
                                ->imageEditorAspectRatios([
                                    '1:1',
                                ])
                                ->maxSize(2048),
                        ]),
                ])->columns(1),

            Section::make('Thông tin hệ thống')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Forms\Components\DateTimePicker::make('email_verified_at')
                                ->label('Thời gian xác thực email')
                                ->disabled()
                                ->visibleOn('edit'),
                            Forms\Components\DateTimePicker::make('last_login_at')
                                ->label('Lần đăng nhập cuối')
                                ->disabled()
                                ->visibleOn('edit'),
                        ]),
                ])
                ->columns(1)
                ->visibleOn('edit'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('Avatar')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(function ($record) {
                        $default = match($record->gender) {
                            'female' => asset('images/default-avatar-female.png'),
                            'male' => asset('images/default-avatar-male.png'),
                            default => asset('images/default-avatar.png')
                        };
                        return $default;
                    }),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Họ và tên')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Điện thoại')
                    ->searchable()
                    ->copyable()
                    ->placeholder('Chưa có'),
                Tables\Columns\TextColumn::make('gender')
                    ->label('Giới tính')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'male' => 'info',
                        'female' => 'success',
                        'other' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'male' => 'Nam',
                        'female' => 'Nữ',
                        'other' => 'Khác',
                        default => 'Chưa xác định',
                    }),
                Tables\Columns\TextColumn::make('age')
                    ->label('Tuổi')
                    ->getStateUsing(function ($record) {
                        return $record->age ? $record->age . ' tuổi' : 'N/A';
                    })
                    ->sortable(query: function ($query, string $direction): \Illuminate\Database\Eloquent\Builder {
                        return $query->orderBy('date_of_birth', $direction === 'asc' ? 'desc' : 'asc');
                    }),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Xác thực')
                    ->boolean()
                    ->getStateUsing(fn ($record) => !is_null($record->email_verified_at))
                    ->tooltip(fn ($record) => $record->email_verified_at ? 
                        'Đã xác thực: ' . $record->email_verified_at->format('d/m/Y H:i') : 
                        'Chưa xác thực email'),
                Tables\Columns\IconColumn::make('status')
                    ->label('Hoạt động')
                    ->boolean()
                    ->tooltip(fn ($record) => $record->status ? 'Đang hoạt động' : 'Tạm khóa'),
                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('Đăng nhập cuối')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Chưa đăng nhập')
                    ->tooltip(fn ($record) => $record->last_login_at ? 
                        $record->last_login_at->diffForHumans() : 
                        'Chưa từng đăng nhập'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        '1' => 'Hoạt động',
                        '0' => 'Tạm khóa',
                    ]),
                Tables\Filters\SelectFilter::make('gender')
                    ->label('Giới tính')
                    ->options([
                        'male' => 'Nam',
                        'female' => 'Nữ', 
                        'other' => 'Khác',
                    ]),
                Tables\Filters\Filter::make('email_verified')
                    ->label('Đã xác thực email')
                    ->query(fn ($query) => $query->whereNotNull('email_verified_at')),
                Tables\Filters\Filter::make('recently_active')
                    ->label('Hoạt động gần đây')
                    ->query(fn ($query) => $query->where('last_login_at', '>=', now()->subDays(30))),
                Tables\Filters\Filter::make('created_this_month')
                    ->label('Đăng ký tháng này')
                    ->query(fn ($query) => $query->whereMonth('created_at', now()->month)),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('verify_email')
                        ->label('Xác thực email')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->visible(fn ($record) => is_null($record->email_verified_at))
                        ->requiresConfirmation()
                        ->action(fn ($record) => $record->update(['email_verified_at' => now()]))
                        ->successNotificationTitle('Đã xác thực email thành công'),
                    Tables\Actions\Action::make('toggle_status')
                        ->label(fn ($record) => $record->status ? 'Khóa tài khoản' : 'Kích hoạt')
                        ->icon(fn ($record) => $record->status ? 'heroicon-o-lock-closed' : 'heroicon-o-lock-open')
                        ->color(fn ($record) => $record->status ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->action(fn ($record) => $record->update(['status' => !$record->status]))
                        ->successNotificationTitle(fn ($record) => 
                            $record->status ? 'Đã kích hoạt tài khoản' : 'Đã khóa tài khoản'),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Kích hoạt')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['status' => 1]))
                        ->successNotificationTitle('Đã kích hoạt các tài khoản được chọn'),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Khóa tài khoản')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['status' => 0]))
                        ->successNotificationTitle('Đã khóa các tài khoản được chọn'),
                    Tables\Actions\BulkAction::make('verify_emails')
                        ->label('Xác thực email')
                        ->icon('heroicon-o-check-badge')
                        ->color('info')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each(function ($record) {
                            if (is_null($record->email_verified_at)) {
                                $record->update(['email_verified_at' => now()]);
                            }
                        }))
                        ->successNotificationTitle('Đã xác thực email cho các tài khoản được chọn'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(null);
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 100 ? 'warning' : 'primary';
    }
}