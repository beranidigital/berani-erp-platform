<?php

namespace App\Filament\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Actions\Action;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Models\Contracts\FilamentUser;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Auth\SessionGuard;
use Illuminate\Validation\ValidationException;

class AdminLogin extends BaseLogin
{
    use InteractsWithFormActions, InteractsWithForms, WithRateLimiting;

    public ?array $data = [];

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }
        $this->form->fill();
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $e) {
            $this->getRateLimitedNotification($e)?->send();

            return null;
        }

        $data = $this->form->getState();

        /** @var SessionGuard $guard */
        $guard = Filament::auth();

        /** @var EloquentUserProvider $provider */
        $provider = $guard->getProvider();

        $user = $provider->retrieveByCredentials(['email' => $data['email'] ?? null]);

        if (! $user) {
            throw ValidationException::withMessages([
                'data.email' => __('This email address is not registered.'),
            ]);
        }

        if (($user instanceof FilamentUser) && (! $user->canAccessPanel(Filament::getCurrentOrDefaultPanel()))) {
            throw ValidationException::withMessages([
                'data.email' => __('Your account cannot access this panel.'),
            ]);
        }

        if (! $provider->validateCredentials($user, ['password' => $data['password'] ?? ''])) {
            throw ValidationException::withMessages([
                'data.password' => __('The password you entered is incorrect.'),
            ]);
        }

        $guard->login($user, $data['remember'] ?? false);
        session()->regenerate();

        // Ensure Livewire triggers a client-side redirect without requiring a manual refresh
        $this->redirectIntended(Filament::getUrl(), navigate: true);

        return null;
    }

    protected function getRateLimitedNotification(TooManyRequestsException $e): ?Notification
    {
        return Notification::make()
            ->title(__('Too many attempts. Try again in :seconds seconds.', [
                'seconds' => $e->secondsUntilAvailable,
                'minutes' => $e->minutesUntilAvailable,
            ]))
            ->danger();
    }

    public function form(Schema $schema): Schema
    {
        return $schema;
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeSchema()
                    ->components([
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getRememberFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label(__('Email'))
            ->email()
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('Password'))
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->autocomplete('current-password')
            ->required()
            ->extraInputAttributes(['tabindex' => 2]);
    }

    protected function getRememberFormComponent(): Component
    {
        return Checkbox::make('remember')
            ->label(__('Remember me'));
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('authenticate')
                ->label(__('Sign in'))
                ->submit('authenticate'),
        ];
    }

    public function getTitle(): string
    {
        return __('Login');
    }

    public function getHeading(): string
    {
        return '';
    }

    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }
}
