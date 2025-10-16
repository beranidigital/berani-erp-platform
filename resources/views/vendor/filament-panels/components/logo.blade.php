@php
    $brandName = filament()->getBrandName();
    // Default logo from panel config
    $brandLogo = filament()->getBrandLogo();
    // Use red logo specifically on admin login page
    $currentPath = request()->path();
    if ($currentPath === 'admin/login') {
        $brandLogo = asset('images/berani-logo-red.svg');
    }
    $brandLogoHeight = filament()->getBrandLogoHeight() ?? '1.5rem';
    $darkModeBrandLogo = filament()->getDarkModeBrandLogo();
    $hasDarkModeBrandLogo = filled($darkModeBrandLogo);

    $getLogoClasses = fn (bool $isDarkMode): string => \Illuminate\Support\Arr::toCssClasses([
        'fi-logo',
        'fi-logo-light' => $hasDarkModeBrandLogo && (! $isDarkMode),
        'fi-logo-dark' => $isDarkMode,
    ]);

    $logoStyles = "height: {$brandLogoHeight}";
@endphp

@capture($content, $logo, $isDarkMode = false)
    @if ($logo instanceof \Illuminate\Contracts\Support\Htmlable)
        <div
            {{
                $attributes
                    ->class([$getLogoClasses($isDarkMode)])
                    ->style([$logoStyles])
            }}
        >
            {{ $logo }}
        </div>
    @elseif (filled($logo))
        <img
            alt="{{ __('filament-panels::layout.logo.alt', ['name' => $brandName]) }}"
            src="{{ $logo }}"
            {{
                $attributes
                    ->class([$getLogoClasses($isDarkMode)])
                    ->style([$logoStyles])
            }}
        />
    @else
        <div
            {{
                $attributes->class([
                    $getLogoClasses($isDarkMode),
                ])->style([$logoStyles])
            }}
        >
            {{-- Render a minimal inline SVG mark that inherits currentColor so CSS can tint it --}}
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 24" aria-hidden="true" focusable="false">
                <title>{{ $brandName }}</title>
                <g fill="currentColor">
                    <text x="0" y="16" font-family="Inter, Arial, Helvetica, sans-serif" font-size="16" font-weight="700">BERANI</text>
                </g>
            </svg>
        </div>
    @endif
@endcapture

{{ $content($brandLogo) }}

@if ($hasDarkModeBrandLogo)
    {{ $content($darkModeBrandLogo, isDarkMode: true) }}
@endif
