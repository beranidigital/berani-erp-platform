<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources\PublicHolidayResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\PublicHolidayResource;

class ViewPublicHoliday extends ViewRecord
{
    protected static string $resource = PublicHolidayResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('time-off::filament/clusters/configurations/resources/public-holiday.pages.view-public-holiday.header-actions.delete.notification.title'))
                        ->body(__('time-off::filament/clusters/configurations/resources/public-holiday.pages.view-public-holiday.header-actions.delete.notification.body'))
                ),
        ];
    }
}

