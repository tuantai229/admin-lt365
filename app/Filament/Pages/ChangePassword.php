<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;

class ChangePassword extends Page
{
    protected static ?string $navigationIcon = null;
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $title = 'Đổi mật khẩu';
    protected ?string $heading = 'Đổi mật khẩu';
    protected static string $view = 'filament.pages.change-password';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('current_password')
                    ->label('Mật khẩu hiện tại')
                    ->password()
                    ->required()
                    ->rule('current_password'),
                TextInput::make('new_password')
                    ->label('Mật khẩu mới')
                    ->password()
                    ->required()
                    ->different('current_password')
                    ->confirmed(),
                TextInput::make('new_password_confirmation')
                    ->label('Xác nhận mật khẩu mới')
                    ->password()
                    ->required(),
            ])
            ->statePath('data');
    }

    public function submit()
    {
        $data = $this->form->getState();

        $user = auth()->user();

        if (!Hash::check($data['current_password'], $user->password)) {
            Notification::make()
                ->title('Mật khẩu hiện tại không đúng')
                ->danger()
                ->send();
            return;
        }

        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);

        Notification::make()
            ->title('Đổi mật khẩu thành công')
            ->success()
            ->send();
        
        $this->form->fill();
    }
}
