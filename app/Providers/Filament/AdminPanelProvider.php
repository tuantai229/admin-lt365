<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use App\Filament\Pages\ChangePassword;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->authGuard('admin')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                \App\Http\Middleware\UpdateLastLoginAt::class, //thêm update thời gian đăng nhập
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigationGroups([
                'Dashboard',
                'Nội dung chính',
                'Giáo dục',
                'Bán hàng',
                'Tương tác',
                'Nội dung phụ',
                'Quản lý Media',
                'Người dùng',
                'Hệ thống',
                'Cài đặt',
            ])
            ->plugins([
                \Awcodes\Curator\CuratorPlugin::make()
                    ->label('Media')
                    ->pluralLabel('Media')
                    ->navigationIcon('heroicon-o-photo')
                    ->navigationGroup('Quản lý Media')
                    ->navigationSort(1)
                    ->navigationCountBadge(),
            ])
            ->userMenuItems([
                'permissions' => MenuItem::make()
                    ->label(function () {
                        $user = auth('admin')->user();
                        $roles = $user->getRoleNames()->toArray();
                        $allPermissions = $user->getAllPermissions();
                        $permissionCount = $allPermissions->count();
                        
                        $displayText = '';
                        if (!empty($roles)) {
                            $displayText .= 'Vai trò: ' . implode(', ', $roles);
                        }
                        if ($permissionCount > 0) {
                            if (!empty($roles)) $displayText .= ' | ';
                            $displayText .= 'Quyền: ' . $permissionCount . ' permissions';
                        }
                        if (empty($roles) && $permissionCount == 0) {
                            $displayText = 'Chưa có vai trò/quyền';
                        }
                        
                        return $displayText;
                    })
                    ->icon('heroicon-o-shield-check')
                    ->url('#')
                    ->openUrlInNewTab(false),
                'password' => MenuItem::make()
                    ->label('Đổi mật khẩu')
                    ->url(fn (): string => ChangePassword::getUrl())
                    ->icon('heroicon-o-key'),
            ]);
    }
}
