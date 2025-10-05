<?php

namespace App\Providers\Filament;

use App\Http\Middleware\SetLocale;
use App\Support\Locale;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Webkul\Support\PluginManager;
use App\Http\Middleware\SetIntendedFromQuery;

class CustomerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('customer')
            ->path('/')
            ->homeUrl(url('/'))
            ->login()
            ->authPasswordBroker('customers')
            ->passwordReset()
            ->registration()
            ->profile(isSimple: false)
            ->favicon(asset('images/berani.ico'))
            ->brandLogo(asset('images/berani-logo.svg'))
            ->darkMode(false)
            ->brandLogoHeight('2rem')
            ->viteTheme('resources/css/filament/theme.css')
            ->colors([
                'primary' => Color::Red,
            ])
            ->topNavigation()
            ->userMenuItems(array_merge(
                [
                    'profile' => Action::make('profile')
                        ->label(fn () => filament()->auth()->user()?->name)
                        ->url(fn (): string => route('filament.customer.auth.profile')),
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

                            return redirect()->route('filament.customer.auth.login');
                        }),
                ],
            ))
            ->plugins([
                PluginManager::make(),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                SetLocale::class . ':customer',
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SetIntendedFromQuery::class,
            ])
            ->authGuard('customer');
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



