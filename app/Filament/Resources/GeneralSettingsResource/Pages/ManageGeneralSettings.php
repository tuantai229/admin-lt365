<?php

namespace App\Filament\Resources\GeneralSettingsResource\Pages;

use App\Filament\Resources\GeneralSettingsResource;
use App\Models\Setting;
use Filament\Actions;
use Filament\Resources\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;

class ManageGeneralSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = GeneralSettingsResource::class;

    protected static string $view = 'filament.resources.general-settings-resource.pages.manage-general-settings';

    // Khai báo các property cho form fields
    public ?string $domain = null;
    public ?string $hotline = null;
    public ?string $email = null;
    public ?string $address = null;
    public ?string $working_hours = null;
    public ?string $footer_intro = null;
    public ?string $facebook = null;
    public ?string $youtube = null;
    public ?string $instagram = null;
    public ?string $tiktok = null;
    public ?array $footer_category_links = [];
    public ?array $footer_support_links = [];

    public function mount(): void
    {
        // Load existing data
        $generalSettings = Setting::getGeneralSettings();
        $footerCategoryLinks = Setting::getFooterCategoryLinks();
        $footerSupportLinks = Setting::getFooterSupportLinks();

        // Fill form với data từ settings
        $this->form->fill(array_merge($generalSettings, [
            'footer_category_links' => $footerCategoryLinks,
            'footer_support_links' => $footerSupportLinks,
        ]));
    }

    public function form(Form $form): Form
    {
        return GeneralSettingsResource::form($form);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        // Lưu general settings (loại bỏ footer links)
        $generalData = $data;
        unset($generalData['footer_category_links'], $generalData['footer_support_links']);
        
        Setting::set('general_settings', $generalData);
        
        // Lưu footer links riêng biệt
        if (isset($data['footer_category_links'])) {
            Setting::set('footer_category_links', $data['footer_category_links']);
        }
        
        if (isset($data['footer_support_links'])) {
            Setting::set('footer_support_links', $data['footer_support_links']);
        }

        // Clear cache
        Setting::clearCache();

        Notification::make()
            ->title('Đã lưu cài đặt chung')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('save')
                ->label('Lưu cài đặt')
                ->submit('save')
                ->color('primary'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('save')
                ->label('Lưu cài đặt')
                ->action('save')
                ->color('primary')
                ->icon('heroicon-o-check'),
        ];
    }
}