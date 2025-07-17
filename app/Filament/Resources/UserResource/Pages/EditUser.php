<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\Action::make('reset_password')
                ->label('Đặt lại mật khẩu')
                ->icon('heroicon-o-key')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Đặt lại mật khẩu')
                ->modalDescription('Bạn có chắc chắn muốn đặt lại mật khẩu cho người dùng này? Mật khẩu mới sẽ là "password".')
                ->action(function () {
                    $this->record->update([
                        'password' => bcrypt('password')
                    ]);
                    $this->notification()
                        ->title('Đã đặt lại mật khẩu thành công')
                        ->body('Mật khẩu mới là: password')
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Thông tin người dùng đã được cập nhật!';
    }
}