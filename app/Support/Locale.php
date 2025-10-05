<?php

namespace App\Support;

use Illuminate\Support\Collection;

class Locale
{
    /**
     * Get the configured locales as a collection.
     */
    public static function all(): Collection
    {
        return collect(config('locales.supported', []));
    }

    /**
     * Get the locale options keyed by locale code.
     */
    public static function options(bool $useNativeLabels = false): array
    {
        return self::all()
            ->mapWithKeys(function (array $definition, string $code) use ($useNativeLabels) {
                $label = $useNativeLabels
                    ? ($definition['native_label'] ?? $definition['label'] ?? strtoupper($code))
                    : ($definition['label'] ?? $definition['native_label'] ?? strtoupper($code));
                $label = __($label);
                return [$code => $label];
            })
            ->all();
    }
}
