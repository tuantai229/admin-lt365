<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $permissions = $this->record->permissions->pluck('name')->toArray();
        $data['permissions_map'] = array_fill_keys($permissions, true);
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $permissions = collect($data['permissions_map'] ?? [])
            ->filter(fn ($value) => $value)
            ->keys()
            ->all();

        /** @var Role $record */
        $record->update([
            'name' => $data['name'],
        ]);
        
        $record->syncPermissions($permissions);

        return $record;
    }
}
