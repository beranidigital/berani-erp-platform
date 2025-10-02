<?php

namespace Webkul\Website\Http\Responses;

use Filament\Facades\Filament;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        // For the customer panel, always go to home
        if (Filament::getCurrentPanel()?->getId() === 'customer') {
            return new RedirectResponse(url('/'));
        }

        // Fallback: default behavior for other panels
        $intendedUrl = redirect()->getIntendedUrl() ?: Filament::getUrl();
        return new RedirectResponse($intendedUrl);
    }
}



