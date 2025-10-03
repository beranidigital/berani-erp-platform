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
                </div>

                {{ FilamentView::renderHook(\Filament\View\PanelsRenderHook::CONTENT_END, scopes: $renderHookScopes) }}
            </main>

            {{ FilamentView::renderHook(\Filament\View\PanelsRenderHook::CONTENT_AFTER, scopes: $renderHookScopes) }}
            {{ FilamentView::renderHook(\Filament\View\PanelsRenderHook::FOOTER, scopes: $renderHookScopes) }}
        </div>

        {{ FilamentView::renderHook(\Filament\View\PanelsRenderHook::LAYOUT_END, scopes: $renderHookScopes) }}
    </div>
</x-filament-panels::layout.base>

@php
    $footerNavigationItems = ($footerNavigationItems ?? collect()) instanceof \Illuminate\Support\Collection
        ? $footerNavigationItems
        : collect($footerNavigationItems ?? []);
    $footerContacts = $contacts ?? [];
    $footerSocialLinks = ($socialLinks ?? collect()) instanceof \Illuminate\Support\Collection
        ? $socialLinks
        : collect($socialLinks ?? []);
@endphp

<div class="relative mt-16 w-screen -translate-x-1/2 left-1/2">
    @include('website::filament.customer.footer.index', [
        'navigationItems' => $footerNavigationItems,
        'contacts'        => $footerContacts,
        'socialLinks'     => $footerSocialLinks,
    ])
</div>
