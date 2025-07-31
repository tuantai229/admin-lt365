<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use Spatie\Permission\Models\Permission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'Hệ thống';

    protected static ?string $navigationLabel = 'Quyền';

    protected static ?string $modelLabel = 'Quyền';

    protected static ?string $pluralModelLabel = 'Quyền';

    protected static ?int $navigationSort = 3;

    public static function shouldRegisterNavigation(): bool
    {
        return auth('admin')->user()->can('view_any_permissions');
    }

    public static function canViewAny(): bool
    {
        return auth('admin')->user()->can('view_any_permissions');
    }

    // Read-only resource
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->label('Tên quyền')->searchable(),
                TextColumn::make('created_at')->label('Ngày tạo')->dateTime('d-m-Y'),
            ])
            ->filters([
                //
            ])
            ->actions([
                // No actions for read-only
            ])
            ->bulkActions([
                // No bulk actions for read-only
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermissions::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('guard_name', 'admin');
    }
}
