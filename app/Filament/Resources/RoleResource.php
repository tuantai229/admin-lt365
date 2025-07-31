<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Section;
use Illuminate\Support\Str;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-finger-print';

    protected static ?string $navigationGroup = 'Hệ thống';
    
    protected static ?string $navigationLabel = 'Phân quyền';

    protected static ?string $modelLabel = 'Phân quyền';

    protected static ?string $pluralModelLabel = 'Phân quyền';

    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        return auth('admin')->user()->can('view_any_roles');
    }

    public static function canViewAny(): bool
    {
        return auth('admin')->user()->can('view_any_roles');
    }

    public static function canCreate(): bool
    {
        return auth('admin')->user()->can('create_roles');
    }

    public static function canEdit($record): bool
    {
        return auth('admin')->user()->can('update_roles');
    }

    public static function canDelete($record): bool
    {
        return auth('admin')->user()->can('delete_roles');
    }

    public static function form(Form $form): Form
    {
        $permissionGroups = self::getPermissionGroups();

        $schema = [
            Section::make()
                ->schema([
                    TextInput::make('name')
                        ->label('Tên phân quyền')
                        ->minLength(2)
                        ->maxLength(255)
                        ->required()
                        ->unique(ignoreRecord: true),
                ])
        ];

        foreach ($permissionGroups as $groupName => $permissions) {
            $checkboxes = collect($permissions)->map(function ($label, $name) {
                return Checkbox::make('permissions_map.' . $name)
                    ->label($label);
            })->all();

            $schema[] = Section::make($groupName)
                ->schema($checkboxes)
                ->columns(4);
        }

        return $form->schema($schema);
    }

    private static function getPermissionGroups(): array
    {
        $permissions = Permission::all();
        $groupedPermissions = [];

        $translationMap = [
            'view_any' => 'Xem danh sách',
            'view' => 'Xem chi tiết',
            'create' => 'Tạo mới',
            'update' => 'Cập nhật',
            'delete' => 'Xóa',
            'delete_any' => 'Xóa nhiều',
            'restore' => 'Phục hồi',
            'restore_any' => 'Phục hồi nhiều',
            'force_delete' => 'Xóa vĩnh viễn',
            'force_delete_any' => 'Xóa vĩnh viễn nhiều',
            'replicate' => 'Nhân bản',
            'reorder' => 'Sắp xếp lại',
            'attach' => 'Đính kèm',
            'detach' => 'Gỡ đính kèm',
            'export' => 'Xuất file',
        ];

        foreach ($permissions as $permission) {
            $parts = explode('_', $permission->name);
            $actionKey = $parts[0];
            if (count($parts) > 2 && in_array($parts[1], ['any'])) {
                $actionKey = $parts[0] . '_' . $parts[1];
            }

            $translatedAction = $translationMap[$actionKey] ?? Str::ucfirst(str_replace('_', ' ', $actionKey));
            $permissionLabel = $translatedAction . ' (' . $permission->name . ')';
            
            $moduleName = Str::ucfirst(str_replace('-', ' ', last($parts)));

            $groupedPermissions[$moduleName][$permission->name] = $permissionLabel;
        }
        
        ksort($groupedPermissions);

        return $groupedPermissions;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->label('Tên phân quyền')->searchable(),
                TextColumn::make('created_at')->label('Ngày tạo')->dateTime('d-m-Y'),
            ])
            ->filters([
                //
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('guard_name', 'admin');
    }
}
