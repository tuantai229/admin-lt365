<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminUserResource\Pages;
use App\Models\AdminUser;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Helpers\FormatHelper;

class AdminUserResource extends Resource
{
    protected static ?string $model = AdminUser::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Hệ thống';
    protected static ?string $navigationLabel = 'Tài khoản quản trị';
    protected static ?string $modelLabel = 'Tài khoản quản trị';
    protected static ?string $pluralModelLabel = 'Tài khoản quản trị';
    protected static ?int $navigationSort = 1;

    // Kiểm tra permission trực tiếp
    public static function shouldRegisterNavigation(): bool
    {
        return auth('admin')->user()->can('view_any_admin_users');
    }

    public static function canViewAny(): bool
    {
        return auth('admin')->user()->can('view_any_admin_users');
    }

    public static function canCreate(): bool
    {
        return auth('admin')->user()->can('create_admin_users');
    }

    public static function canEdit($record): bool
    {
        return auth('admin')->user()->can('update_admin_users');
    }

    public static function canDelete($record): bool
    {
        return auth('admin')->user()->can('delete_admin_users') 
               && $record->id !== auth('admin')->id(); // Không xóa chính mình
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('username')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            Forms\Components\TextInput::make('email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            Forms\Components\TextInput::make('password')
                ->password()
                ->required()
                ->hiddenOn('edit'),
            Forms\Components\Select::make('roles')
                ->relationship('roles', 'name')
                ->multiple()
                ->preload()
                ->searchable(),
            Forms\Components\Toggle::make('status')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('username')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Phân quyền')
                    ->badge()
                    ->separator(','),
                Tables\Columns\ToggleColumn::make('status'),
                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('Lần đăng nhập cuối')
                    ->formatStateUsing(function ($state) {
                        if ($state === null) {
                            return 'Chưa đăng nhập';
                        }
                        return FormatHelper::datetime($state);
                    })
                    ->placeholder('N/A'),
                Tables\Columns\TextColumn::make('created_at')
                    ->formatStateUsing(fn ($state) => FormatHelper::datetime($state))
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdminUsers::route('/'),
            'create' => Pages\CreateAdminUser::route('/create'),
            'edit' => Pages\EditAdminUser::route('/{record}/edit'),
        ];
    }
}
