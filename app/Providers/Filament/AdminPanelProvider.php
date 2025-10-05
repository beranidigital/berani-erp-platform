<?php

namespace App\Providers\Filament;

use App\Http\Middleware\SetLocale;
use App\Support\Locale;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Webkul\Support\Filament\Pages\Profile;
use Webkul\Support\PluginManager;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        set_time_limit(300);

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(\App\Filament\Auth\AdminLogin::class)
            ->favicon(asset('images/berani.ico'))
            ->brandLogo(asset('images/berani-logo.svg'))
            ->brandLogoHeight('2rem')
            ->viteTheme('resources/css/filament/theme.css')
            ->passwordReset()
            ->emailVerification()
            ->profile()
            ->colors([
                'primary' => Color::Red,
            ])
            ->unsavedChangesAlerts()
            // ->spa()
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth(Width::Full)
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Dashboard'),
                NavigationGroup::make()
                    ->label('Settings'),
            ])
            ->userMenuItems(array_merge(
                [
                    'profile' => Action::make('profile')
                        ->label(fn () => filament()->auth()->user()?->name)
                        ->url(fn (): string => Profile::getUrl()),
                ],
                $this->getLocaleUserMenuActions(),
                [
                    'logout' => Action::make('logout')
                        ->label(__('Logout'))
                        ->icon('heroicon-o-arrow-left-on-rectangle')
                        ->requiresConfirmation()
                        ->action(function () {
                            Filament::auth()->logout();

                            request()->session()->invalidate();
                            request()->session()->regenerateToken();

                            return redirect()->route('filament.admin.auth.login');
                        }),
                ],
            ))
            ->plugins([
                FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 1,
                        'sm'      => 1,
                        'lg'      => 2,
                        'xl'      => 3,
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm'      => 1,
                        'lg'      => 2,
                        'xl'      => 3,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm'      => 2,
                    ]),
                PluginManager::make(),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                SetLocale::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    protected function getLocaleUserMenuActions(): array
    {
        $localeActions = collect(Locale::options(true))
            ->mapWithKeys(fn (string $label, string $code) => ["locale-{$code}" => Action::make("set-locale-{$code}")
                ->label($label)
                ->icon(app()->getLocale() === $code ? 'heroicon-o-check' : null)
                ->url(fn (): string => request()->fullUrlWithQuery(['locale' => $code]))
            ])->all();

        if (empty($localeActions)) {
            return [];
        }

        return array_merge([
            'locale-label' => Action::make('locale-label')
                ->label(__('Language'))
                ->icon('heroicon-o-language')
                ->disabled(),
        ], $localeActions);
    }
}

