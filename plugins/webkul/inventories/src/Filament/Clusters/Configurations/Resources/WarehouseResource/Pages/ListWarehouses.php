<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource\Pages;

use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Models\Warehouse;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource;

class ListWarehouses extends ListRecords
{
    protected static string $resource = WarehouseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createWarehouseTemplate')
                ->label(__('inventories::filament/clusters/configurations/resources/warehouse/pages/list-warehouses.header-actions.create-template.label'))
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->url(WarehouseResource::getUrl('create'))
                ->visible(fn () => WarehouseResource::canCreate())
                ->visible(WarehouseResource::getWarehouseSettings()->enable_locations)
                ->mutateDataUsing(function ($data) {
                    $user = Auth::user();

                    $data['creator_id'] = $user->id;

                    $data['company_id'] = $user->defaultCompany?->id;

                    return $data;
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('inventories::filament/clusters/configurations/resources/warehouse/pages/list-warehouses.header-actions.create.notification.title'))
                        ->body(__('inventories::filament/clusters/configurations/resources/warehouse/pages/list-warehouses.header-actions.create.notification.body')),
                ),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('inventories::filament/clusters/configurations/resources/warehouse/pages/list-warehouses.tabs.all'))
                ->badge(Warehouse::count()),
            'archived' => Tab::make(__('inventories::filament/clusters/configurations/resources/warehouse/pages/list-warehouses.tabs.archived'))
                ->badge(Warehouse::onlyTrashed()->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->onlyTrashed();
                }),
        ];
    }
}
