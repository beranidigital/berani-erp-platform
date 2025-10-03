<?php

namespace Webkul\Website\Http\Responses;

use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        $panel = Filament::getCurrentPanel();
        $panelId = $panel?->getId();
        $intendedUrl = redirect()->getIntendedUrl();
        $loginUrl = $panel?->getLoginUrl();

        if ($panelId === 'customer') {
            $targetUrl = $intendedUrl ?: url('/');

            if ($loginUrl && $this->urlsMatch($targetUrl, $loginUrl)) {
                $targetUrl = url('/');
            }

            $this->clearIntendedUrl();

            return new RedirectResponse($targetUrl);
        }

        $defaultUrl = Filament::getUrl() ?? url('/admin');
        $targetUrl = $intendedUrl ?: $defaultUrl;

        if ($loginUrl && $this->urlsMatch($targetUrl, $loginUrl)) {
            $targetUrl = $defaultUrl;
        }

        $this->clearIntendedUrl();

        return new RedirectResponse($targetUrl);
    }

    protected function clearIntendedUrl(): void
    {
        session()->forget('url.intended');
    }

    protected function urlsMatch(?string $first, ?string $second): bool
    {
        if (! $first || ! $second) {
            return false;
        }

        return $this->normalizePath($first) === $this->normalizePath($second);
    }

    protected function normalizePath(string $url): string
    {
        $parsed = parse_url($url);

        if ($parsed === false) {
            return trim($url, '/');
        }

        $path = Arr::get($parsed, 'path', '/');

        return rtrim($path, '/') ?: '/';
    }
}

