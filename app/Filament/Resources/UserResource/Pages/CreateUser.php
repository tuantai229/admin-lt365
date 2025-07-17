<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set email_verified_at for new users created by admin
        $data['email_verified_at'] = now();
        
        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Tài khoản người dùng đã được tạo thành công!';
    }
}