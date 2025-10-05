<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource;
use Webkul\Inventory\Models\Warehouse;

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
                ->visible(fn () => WarehouseResource::canCreate()),
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
