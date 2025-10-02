<header class="bg-white/80 backdrop-blur border-b border-slate-200/60">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-14 items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2 text-slate-800 font-semibold">
                <img src="{{ asset('images/berani-logo.svg') }}" alt="{{ config('app.name') }}" class="h-6" />
                <span class="hidden sm:inline">{{ config('app.name') }}</span>
            </a>

            <nav class="flex items-center gap-4 text-sm text-slate-600">
                <a href="{{ url('/') }}" class="hover:text-primary-600">{{ __('Home') }}</a>
                <a href="{{ route('recruitments.careers.index') }}" class="hover:text-primary-600">{{ __('Careers') }}</a>
                @auth('customer')
                    <a href="{{ filament()->getProfileUrl() }}" class="hover:text-primary-600">{{ __('My Account') }}</a>
                    <form action="{{ filament()->getLogoutUrl() }}" method="POST">
                        @csrf
                        <button type="submit" class="text-slate-600 hover:text-primary-600">{{ __('Logout') }}</button>
                    </form>
                @else
                    <a href="{{ filament()->getLoginUrl() }}" class="hover:text-primary-600">{{ __('Login') }}</a>
                    <a href="{{ filament()->getRegistrationUrl() }}" class="rounded-lg border border-primary-200 px-3 py-1.5 text-primary-600 hover:bg-primary-50">{{ __('Sign up') }}</a>
                @endauth
            </nav>
        </div>
    </div>
</header>



