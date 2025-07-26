<?php

namespace App\Filament\Resources\HomeSettingsResource\Pages;

use App\Filament\Resources\HomeSettingsResource;
use App\Models\Setting;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Filament\Forms\Form;
use Filament\Actions;
use Filament\Notifications\Notification;

class ManageHomeSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = HomeSettingsResource::class;
    
    protected static string $view = 'filament.resources.home-settings-resource.pages.manage-home-settings';

    // Declare public properties for all form fields
    public ?array $hero_slides = [];
    public ?string $quick_transfer_title = null;
    public ?array $quick_transfer_boxes = [];
    public ?int $news_category_id = null;
    public ?array $selected_centers = [];
    public ?array $selected_teachers = [];
    public ?string $stats_documents = null;
    public ?string $stats_schools = null;
    public ?string $stats_members = null;
    public ?string $stats_rating = null;
    public ?array $parent_reviews = [];

    public function mount(): void
    {
        // Get all settings data
        $heroSlides = Setting::get('home_hero_slides', []);
        $quickTransfer = Setting::get('home_quick_transfer', [
            'title' => 'Đồng hành cùng con vào trường chuyên',
            'boxes' => []
        ]);
        $newsSchedule = Setting::get('home_news_schedule', [
            'selected_category_id' => null
        ]);
        $teachersCenter = Setting::get('home_teachers_centers', [
            'centers' => [],
            'teachers' => []
        ]);
        $statsReviews = Setting::get('home_stats_reviews', [
            'stats' => [
                'documents' => '10,000+',
                'schools' => '500+',
                'members' => '50,000+',
                'rating' => '4.8/5'
            ],
            'reviews' => []
        ]);

        $this->form->fill([
            'hero_slides' => $heroSlides,
            'quick_transfer_title' => $quickTransfer['title'] ?? 'Đồng hành cùng con vào trường chuyên',
            'quick_transfer_boxes' => $quickTransfer['boxes'] ?? [],
            'news_category_id' => $newsSchedule['selected_category_id'] ?? null,
            'selected_centers' => $teachersCenter['centers'] ?? [],
            'selected_teachers' => $teachersCenter['teachers'] ?? [],
            'stats_documents' => $statsReviews['stats']['documents'] ?? '10,000+',
            'stats_schools' => $statsReviews['stats']['schools'] ?? '500+',
            'stats_members' => $statsReviews['stats']['members'] ?? '50,000+',
            'stats_rating' => $statsReviews['stats']['rating'] ?? '4.8/5',
            'parent_reviews' => $statsReviews['reviews'] ?? [],
        ]);
    }

    public function form(Form $form): Form
    {
        return HomeSettingsResource::form($form);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Save Hero Slides
        Setting::set('home_hero_slides', $data['hero_slides'] ?? []);

        // Save Quick Transfer
        Setting::set('home_quick_transfer', [
            'title' => $data['quick_transfer_title'] ?? 'Đồng hành cùng con vào trường chuyên',
            'boxes' => $data['quick_transfer_boxes'] ?? []
        ]);

        // Save News Schedule
        Setting::set('home_news_schedule', [
            'selected_category_id' => $data['news_category_id'] ?? null
        ]);

        // Save Teachers & Centers
        Setting::set('home_teachers_centers', [
            'centers' => $data['selected_centers'] ?? [],
            'teachers' => $data['selected_teachers'] ?? []
        ]);

        // Save Stats & Reviews
        Setting::set('home_stats_reviews', [
            'stats' => [
                'documents' => $data['stats_documents'] ?? '10,000+',
                'schools' => $data['stats_schools'] ?? '500+',
                'members' => $data['stats_members'] ?? '50,000+',
                'rating' => $data['stats_rating'] ?? '4.8/5'
            ],
            'reviews' => $data['parent_reviews'] ?? []
        ]);

        // Clear settings cache
        Setting::clearCache();

        Notification::make()
            ->title('✅ Đã lưu thành công!')
            ->body('Cài đặt trang chủ đã được cập nhật.')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('save')
                ->label('💾 Lưu cài đặt trang chủ')
                ->submit('save')
                ->color('primary'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('save')
                ->label('💾 Lưu cài đặt trang chủ')
                ->action('save')
                ->color('primary')
                ->icon('heroicon-o-check'),
        ];
    }
}
