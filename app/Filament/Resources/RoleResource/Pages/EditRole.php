<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $permissionGroups = RoleResource::getPermissionGroups();
        $rolePermissions = $this->record->permissions->pluck('name')->toArray();
        $map = [];

        foreach ($permissionGroups as $groupName => $permissions) {
            $groupSlug = Str::slug($groupName);
            $map[$groupSlug] = [];
            foreach (array_keys($permissions) as $permissionName) {
                if (in_array($permissionName, $rolePermissions)) {
                    $map[$groupSlug][] = $permissionName;
                }
            }
        }

        $data['permissions_map'] = $map;
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $allPermissions = [];
        if (isset($data['permissions_map']) && is_array($data['permissions_map'])) {
            foreach ($data['permissions_map'] as $groupPermissions) {
                if (is_array($groupPermissions)) {
                    $allPermissions = array_merge($allPermissions, $groupPermissions);
                }
            }
        }
        
        $uniquePermissions = array_unique($allPermissions);

        /** @var Role $record */
        $record->update(['name' => $data['name']]);
        $record->syncPermissions($uniquePermissions);

        return $record;
    }
}
