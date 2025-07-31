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
use Filament\Forms\Components\CheckboxList;
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
            $schema[] = Section::make($groupName)
                ->schema([
                    CheckboxList::make('permissions_map.' . Str::slug($groupName))
                        ->label('')
                        ->options($permissions)
                        ->bulkToggleable()
                        ->columns(4),
                ])
                ->collapsible();
        }

        return $form->schema($schema);
    }

    public static function getPermissionGroups(): array
    {
        $moduleNames = [
            'documents' => 'Quản lý Tài liệu',
            'levels' => 'Quản lý Cấp học',
            'subjects' => 'Quản lý Môn học',
            'document_types' => 'Quản lý Loại tài liệu',
            'difficulty_levels' => 'Quản lý Độ khó',
            'schools' => 'Quản lý Trường học',
            'school_types' => 'Quản lý Loại trường',
            'news' => 'Quản lý Tin tức',
            'news_categories' => 'Quản lý Danh mục tin tức',
            'pages' => 'Quản lý Trang đơn',
            'teachers' => 'Quản lý Giáo viên',
            'centers' => 'Quản lý Trung tâm',
            'orders' => 'Quản lý Đơn hàng',
            'comments' => 'Quản lý Bình luận',
            'ratings' => 'Quản lý Đánh giá',
            'contacts' => 'Quản lý Liên hệ',
            'newsletters' => 'Quản lý Đăng ký bản tin',
            'tags' => 'Quản lý Tags',
            'admin_users' => 'Quản lý Tài khoản quản trị',
            'users' => 'Quản lý Tài khoản người dùng',
            'roles' => 'Quản lý Phân quyền',
            'permissions' => 'Quản lý Quyền',
            'settings' => 'Quản lý Cài đặt',
        ];

        $actionNames = [
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
        ];

        $permissions = Permission::all();
        $groupedPermissions = [];

        foreach ($permissions as $permission) {
            $foundAction = null;
            $foundModuleKey = null;

            $bestMatchAction = '';
            foreach (array_keys($actionNames) as $actionKey) {
                if (str_starts_with($permission->name, $actionKey . '_')) {
                    if (strlen($actionKey) > strlen($bestMatchAction)) {
                        $bestMatchAction = $actionKey;
                    }
                }
            }

            if ($bestMatchAction) {
                $foundAction = $bestMatchAction;
                $foundModuleKey = substr($permission->name, strlen($foundAction) + 1);
            }

            if ($foundAction && isset($moduleNames[$foundModuleKey])) {
                $moduleLabel = $moduleNames[$foundModuleKey];
                $actionLabel = $actionNames[$foundAction];
                $permissionLabel = $actionLabel . ' (' . $permission->name . ')';
                $groupedPermissions[$moduleLabel][$permission->name] = $permissionLabel;
            } else {
                $groupedPermissions['Khác'][$permission->name] = str_replace('_', ' ', $permission->name);
            }
        }

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
