<?php

namespace App\Providers\Filament;

use App\Filament\Resources\DashboardResource\Widgets\LatestOrders;
use App\Filament\Resources\DashboardResource\Widgets\OrdersChart;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationItem;
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
use PhpParser\Node\Stmt\Label;
use App\Filament\Resources\DashboardResource\Widgets\StatsOverview;
use App\Filament\Resources\DashboardResource\Widgets\ProductsChart;
use Filament\Pages\Dashboard;
use pxlrbt\FilamentSpotlight\SpotlightPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('dashboard')
            ->path('dashboard')
            ->login()
            ->colors([
                'primary' => Color::Sky,
            ])
            ->globalSearchKeyBindings(['command+k','ctrl+k'])
            ->navigationItems([
                NavigationItem::make('Blog')
                ->url('https://kprog.app', shouldOpenInNewTab:true)
                ->icon('heroicon-o-pencil-square')
                ->group('External')
                ->sort(2)
            ])
            ->userMenuItems([
                MenuItem::make()
                ->label('Settings')
                ->url('')
                ->icon('heroicon-o-cog-6-tooth'),
                'logout' => MenuItem::make()->Label('Log Out')
            ])
                ->plugins([
                    SpotlightPlugin::make()
                ])
            ->breadcrumbs(false)
            ->font(family:"Poppins")
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->widgets([
                StatsOverview::class,  // ✅ Register Stats Overview
                ProductsChart::class,  // ✅ Register Products Chart
                OrdersChart::class,    // ✅ Register Orders Chart
                LatestOrders::class,   // ✅ Register Latest Orders Widget
            ])
            ->pages([
                Dashboard::class, // ✅ Ensure Dashboard is registered
            ])
            ->authMiddleware([
                Authenticate::class, 
            ]);
    }
}
