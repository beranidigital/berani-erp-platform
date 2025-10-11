<?php

namespace Webkul\Account\Filament\Resources\AccountResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Account\Filament\Resources\AccountResource;

class EditAccount extends EditRecord
{
    protected static string $resource = AccountResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('accounts::filament/resources/account/pages/edit-account.notification.title'))
            ->body(__('accounts::filament/resources/account/pages/edit-account.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('accounts::filament/resources/account/pages/edit-account.header-actions.delete.notification.title'))
                        ->body(__('accounts::filament/resources/account/pages/edit-account.header-actions.delete.notification.body'))
                ),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['invoices_account_tax']) && is_array($data['invoices_account_tax'])) {
            $data['invoices_account_tax'] = array_values(array_unique($data['invoices_account_tax']));
        }

        return $data;
    }
}
