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
        $permissions = collect($data['permissions_map'] ?? [])
            ->filter(fn ($value) => $value)
            ->keys()
            ->all();

        /** @var Role $role */
        $role = static::getModel()::create([
            'name' => $data['name'],
            'guard_name' => 'admin',
        ]);

        $role->syncPermissions($permissions);

        return $role;
    }
}
