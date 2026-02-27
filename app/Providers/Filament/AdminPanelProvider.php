<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\WisudaMahasiswaPage;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Navigation\NavigationGroup;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('/')
            ->login()
            ->colors([
                'danger' => Color::Red,
                'gray' => Color::Neutral,
                'info' => Color::Blue,
                'primary' => Color::Lime, // Lime-800/900
                'success' => Color::Green,
                'warning' => Color::Yellow,
            ])
            // ->brandLogo(asset('logokampus.jpg'))
            ->brandLogoHeight('3rem')
            ->favicon(asset('favicon.ico'))
            ->font('Outfit')
            ->sidebarCollapsibleOnDesktop()
            ->spa()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
                WisudaMahasiswaPage::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
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
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->navigationGroups([
                NavigationGroup::make('Master Data'),
                NavigationGroup::make('Perkuliahan'),
                NavigationGroup::make('Pendaftaran'),
                NavigationGroup::make('Tugas Akhir'),
                NavigationGroup::make('Pengaturan User'),
                NavigationGroup::make('Temp'),
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                fn(): string => '
                    <meta property="og:image" content="' . asset('logokampus.jpg') . '" />
                    <link rel="manifest" href="/manifest.json">
                    <meta name="apple-mobile-web-app-capable" content="yes">
                    <meta name="apple-mobile-web-app-status-bar-style" content="default">
                    <meta name="apple-mobile-web-app-title" content="SIAKAD">
                    <link rel="apple-touch-icon" href="/logokampus.jpg">
                    <script>
                        if ("serviceWorker" in navigator) {
                            window.addEventListener("load", function() {
                                navigator.serviceWorker.register("/sw.js").then(function(registration) {
                                    console.log("ServiceWorker registration successful with scope: ", registration.scope);
                                }, function(err) {
                                    console.log("ServiceWorker registration failed: ", err);
                                });
                            });
                        }
                    </script>
                '
            )

            ->renderHook(
                \Filament\View\PanelsRenderHook::USER_MENU_BEFORE,
                fn(): string => \Illuminate\Support\Facades\Blade::render('
                    <div class="hidden md:flex flex-col items-end justify-center px-3 text-right">
                        <span class="text-sm font-bold text-gray-900 dark:text-white leading-tight">
                            {{ auth()->user()->name }}
                        </span>
                        <span class="text-[10px] font-medium text-gray-500 dark:text-gray-400 tracking-wider">
                            {{ auth()->user()->getRoleNames()->implode(", ") }}
                        </span>
                    </div>
                ')
            )
            ->viteTheme('resources/css/filament/admin/theme.css');
    }
}
