<?php

namespace Webkul\Account\Filament\Clusters;

use Filament\Clusters\Cluster;

class Accounts extends Cluster
{
    protected static ?int $navigationSort = 600;

    public static function getNavigationLabel(): string
    {
        return __('accounts::filament/clusters/accounts.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('accounts::filament/clusters/accounts.navigation.group');
    }

    public static function canAccess(): bool
    {
        $user = filament()->auth()->user();

        // Show Accounts menu for any authenticated admin user
        return (bool) $user;
    }

    public static function canAccessClusteredComponents(): bool
    {
        return static::canAccess();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }
}
