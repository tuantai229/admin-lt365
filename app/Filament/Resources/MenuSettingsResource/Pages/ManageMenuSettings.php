<?php

namespace App\Filament\Resources\MenuSettingsResource\Pages;

use App\Filament\Resources\MenuSettingsResource;
use App\Models\Setting;
use Filament\Actions;
use Filament\Resources\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;

class ManageMenuSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = MenuSettingsResource::class;

    protected static string $view = 'filament.resources.menu-settings-resource.pages.manage-menu-settings';

    // Khai báo property cho form field
    public ?array $main_navigation = [];

    public function mount(): void
    {
        // Load existing data
        $mainNavigation = Setting::getMainNavigation();

        $this->form->fill([
            'main_navigation' => $mainNavigation,
        ]);
    }

    public function form(Form $form): Form
    {
        return MenuSettingsResource::form($form);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        // Lưu menu navigation
        Setting::set('main_navigation', $data['main_navigation'] ?? []);

        // Clear cache
        Setting::clearCache();

        Notification::make()
            ->title('Đã lưu cài đặt menu')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('save')
                ->label('Lưu cài đặt menu')
                ->submit('save')
                ->color('primary'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('save')
                ->label('Lưu cài đặt menu')
                ->action('save')
                ->color('primary')
                ->icon('heroicon-o-check'),
            
            Actions\Action::make('preview')
                ->label('Xem trước menu')
                ->url('#')
                ->openUrlInNewTab()
                ->color('gray')
                ->icon('heroicon-o-eye'),
        ];
    }
}