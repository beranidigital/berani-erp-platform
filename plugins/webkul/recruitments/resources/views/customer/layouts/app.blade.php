@php
    use Filament\Support\Enums\Width;
    use Filament\Support\Facades\FilamentView;

    \Filament\Facades\Filament::setCurrentPanel('customer');

    $renderHookScopes = [];
    $maxContentWidth = filament()->getMaxContentWidth() ?? Width::SevenExtraLarge;

    if (is_string($maxContentWidth)) {
        $maxContentWidth = Width::tryFrom($maxContentWidth) ?? $maxContentWidth;
    }
@endphp

@push('styles')
    @vite('resources/css/app.css')
@endpush

@push('scripts')
    @vite('resources/js/app.js')
@endpush

<x-filament-panels::layout.base :livewire="null" class="fi-body-has-topbar">
    @if (filament()->hasTopbar())
        {{ FilamentView::renderHook(\Filament\View\PanelsRenderHook::TOPBAR_BEFORE, scopes: $renderHookScopes) }}

        @livewire(filament()->getTopbarLivewireComponent())

        {{ FilamentView::renderHook(\Filament\View\PanelsRenderHook::TOPBAR_AFTER, scopes: $renderHookScopes) }}
    @endif

    <div class="fi-layout">
        {{ FilamentView::renderHook(\Filament\View\PanelsRenderHook::LAYOUT_START, scopes: $renderHookScopes) }}

        <div class="fi-main-ctn">
            {{ FilamentView::renderHook(\Filament\View\PanelsRenderHook::CONTENT_BEFORE, scopes: $renderHookScopes) }}

            <main @class([
                'fi-main',
                $maxContentWidth instanceof Width ? 'fi-width-'.$maxContentWidth->value : $maxContentWidth,
            ])>
                {{ FilamentView::renderHook(\Filament\View\PanelsRenderHook::CONTENT_START, scopes: $renderHookScopes) }}

                <div class="fi-main-content space-y-10">
                    @yield('content')

                    <footer class="border-t border-gray-200 bg-white px-6 py-6 text-sm text-gray-500 shadow-sm sm:rounded-lg sm:px-8">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div class="space-y-1">
                                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}</p>
                                @if (! empty($contacts['email']))
                                    <p>{{ __('Email:') }} <a href="mailto:{{ $contacts['email'] }}" class="hover:text-primary-600">{{ $contacts['email'] }}</a></p>
                                @endif
                                @if (! empty($contacts['phone']))
                                    <p>{{ __('Phone:') }} <a href="tel:{{ $contacts['phone'] }}" class="hover:text-primary-600">{{ $contacts['phone'] }}</a></p>
                                @endif
                            </div>

                            @if (! empty($socialLinks))
                                <div class="flex items-center gap-4">
                                    @foreach ($socialLinks as $link)
                                        <a href="{{ $link['url'] }}" target="_blank" rel="noopener" class="text-gray-500 hover:text-primary-600" aria-label="{{ $link['label'] }}">
                                            {!! $link['icon'] !!}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </footer>
                </div>

                {{ FilamentView::renderHook(\Filament\View\PanelsRenderHook::CONTENT_END, scopes: $renderHookScopes) }}
            </main>

            {{ FilamentView::renderHook(\Filament\View\PanelsRenderHook::CONTENT_AFTER, scopes: $renderHookScopes) }}
            {{ FilamentView::renderHook(\Filament\View\PanelsRenderHook::FOOTER, scopes: $renderHookScopes) }}
        </div>

        {{ FilamentView::renderHook(\Filament\View\PanelsRenderHook::LAYOUT_END, scopes: $renderHookScopes) }}
    </div>
</x-filament-panels::layout.base>
