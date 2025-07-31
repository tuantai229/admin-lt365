<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function handleRecordCreation(array $data): Model
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

        /** @var Role $role */
        $role = static::getModel()::create([
            'name' => $data['name'],
            'guard_name' => 'admin',
        ]);

        $role->syncPermissions($uniquePermissions);

        return $role;
    }
}
