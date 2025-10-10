<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypeResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Enums\ReservationMethod;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypeResource;

class CreateOperationType extends CreateRecord
{
    protected static string $resource = OperationTypeResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('inventories::filament/clusters/configurations/resources/operation-type/pages/create-operation-type.notification.title'))
            ->body(__('inventories::filament/clusters/configurations/resources/operation-type/pages/create-operation-type.notification.body'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure required foreign keys are present
        $data['source_location_id'] ??= OperationTypeResource::resolveDefaultSourceLocationId($data['type'] ?? null, $data['warehouse_id'] ?? null);
        $data['destination_location_id'] ??= OperationTypeResource::resolveDefaultDestinationLocationId($data['type'] ?? null, $data['warehouse_id'] ?? null);

        $data['reservation_method'] = ReservationMethod::AT_CONFIRM;

        $data['creator_id'] = Auth::id();

        return $data;
    }
}
