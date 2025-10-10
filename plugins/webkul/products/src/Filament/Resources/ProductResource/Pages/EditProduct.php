<?php

namespace Webkul\Product\Filament\Resources\ProductResource\Pages;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Product\Filament\Resources\ProductResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditProduct extends EditRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = ProductResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('products::filament/resources/product/pages/edit-product.notification.title'))
            ->body(__('products::filament/resources/product/pages/edit-product.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->setResource(static::$resource),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('products::filament/resources/product/pages/edit-product.header-actions.delete.notification.title'))
                        ->body(__('products::filament/resources/product/pages/edit-product.header-actions.delete.notification.body')),
                ),
        ];
    }

    protected function afterSave(): void
    {
        $this->getRecord()->variants->each(function ($variant) {
            $variant->update([
                'is_storable' => $this->getRecord()->is_storable,
            ]);
        });
    }
}
