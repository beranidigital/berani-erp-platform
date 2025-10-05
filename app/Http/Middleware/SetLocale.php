<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ?string $guard = null): Response
    {
        $supported = array_keys(Config::get('locales.supported', []));

        if (empty($supported)) {
            $supported = [Config::get('app.locale', 'en')];
        }

        $locale = $this->resolveLocale($request, $guard, $supported);

        App::setLocale($locale);
        $request->setLocale($locale);

        return $next($request);
    }

    /**
     * Resolve the locale for the current request.
     */
    protected function resolveLocale(Request $request, ?string $guard, array $supported): string
    {
        $locale = null;

        $requested = $this->normalizeLocale($request->query('locale', $request->query('lang')));

        if ($requested && in_array($requested, $supported, true)) {
            Session::put('locale', $requested);
            $this->persistAuthenticatedLocale($guard, $requested);
            $locale = $requested;
        }

        if (! $locale) {
            $sessionLocale = $this->normalizeLocale(Session::get('locale'));

            if ($sessionLocale && in_array($sessionLocale, $supported, true)) {
                $locale = $sessionLocale;
            }
        }

        $authLocale = $this->resolveAuthenticatedLocale($guard, $supported);

        if ($authLocale) {
            Session::put('locale', $authLocale);
            $locale = $authLocale;
        }

        return $locale ?? Config::get('app.locale', 'en');
    }

    /**
     * Resolve locale from the authenticated user for the provided guard.
     */
    protected function resolveAuthenticatedLocale(?string $guard, array $supported): ?string
    {
        foreach ($this->guardPriorities($guard) as $currentGuard) {
            $auth = Auth::guard($currentGuard);

            if (! $auth->check()) {
                continue;
            }

            $user = $auth->user();
            $locale = $this->normalizeLocale($user->language ?? $user->lang ?? null);

            if ($locale && in_array($locale, $supported, true)) {
                return $locale;
            }
        }

        return null;
    }

    protected function guardPriorities(?string $guard): array
    {
        return array_values(array_unique(array_filter(
            array_merge([$guard], array_keys(config('auth.guards', []))),
            fn ($value) => filled($value)
        )));
    }

    protected function persistAuthenticatedLocale(?string $guard, string $locale): void
    {
        foreach ($this->guardPriorities($guard) as $currentGuard) {
            $auth = Auth::guard($currentGuard);

            if (! $auth->check()) {
                continue;
            }

            $user = $auth->user();
            $current = $this->normalizeLocale($user->language ?? $user->lang ?? null);

            if ($current === $locale || ! method_exists($user, 'isFillable')) {
                continue;
            }

            if ($user->isFillable('language')) {
                $user->forceFill(['language' => $locale])->save();

                continue;
            }

            if ($user->isFillable('lang')) {
                $user->forceFill(['lang' => $locale])->save();
            }
        }
    }

    /**
     * Normalize the provided locale string.
     */
    protected function normalizeLocale(?string $locale): ?string
    {
        if (! $locale) {
            return null;
        }

        return str_replace(['_', '-'], '-', strtolower($locale));
    }
}