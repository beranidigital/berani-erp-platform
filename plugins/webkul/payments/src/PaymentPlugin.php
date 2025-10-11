<?php

namespace Webkul\Payment;

use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use ReflectionClass;
use Webkul\Support\Package;

class PaymentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'payments';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function register(Panel $panel): void
    {
        if (! Package::isPluginInstalled($this->getId())) {
            return;
        }

        $panel
            ->when($panel->getId() == 'admin', function (Panel $panel) {
                $panel->discoverResources(in: $this->getPluginBasePath('/Filament/Resources'), for: 'Webkul\\Payment\\Filament\\Resources')
                    ->discoverPages(in: $this->getPluginBasePath('/Filament/Pages'), for: 'Webkul\\Payment\\Filament\\Pages')
                    ->discoverClusters(in: $this->getPluginBasePath('/Filament/Clusters'), for: 'Webkul\\Payment\\Filament\\Clusters')
                    ->discoverWidgets(in: $this->getPluginBasePath('/Filament/Widgets'), for: 'Webkul\\Payment\\Filament\\Widgets')
                    ->navigationItems([
                        NavigationItem::make('payments')
                            ->label('Payments')
                            ->url(fn () => \Webkul\Account\Filament\Resources\PaymentsResource::getUrl())
                            ->icon('heroicon-o-banknotes')
                            ->group('Payments')
                            ->sort(1)
                            ->visible(fn (): bool => \Webkul\Account\Filament\Resources\PaymentsResource::canViewAny()),
                    ]);
            });
    }

    public function boot(Panel $panel): void
    {
        //
    }

    protected function getPluginBasePath($path = null): string
    {
        $reflector = new ReflectionClass(get_class($this));

        return dirname($reflector->getFileName()).($path ?? '');
    }
}
