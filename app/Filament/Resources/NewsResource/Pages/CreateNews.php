<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Resources\NewsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateNews extends CreateRecord
{
    protected static string $resource = NewsResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Đảm bảo admin_user_id được set
        $data['admin_user_id'] = auth()->id();
        
        // Nếu không chọn school thì set = 0
        if (empty($data['school_id'])) {
            $data['school_id'] = 0;
        }
        
        return $data;
    }
}
