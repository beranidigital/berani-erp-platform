<?php

namespace Webkul\Account\Filament\Resources\AccountResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Account\Filament\Resources\AccountResource;

class CreateAccount extends CreateRecord
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
            ->title(__('accounts::filament/resources/account/pages/create-account.notification.title'))
            ->body(__('accounts::filament/resources/account/pages/create-account.notification.body'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['invoices_account_tax']) && is_array($data['invoices_account_tax'])) {
            $data['invoices_account_tax'] = array_values(array_unique($data['invoices_account_tax']));
        }

        return $data;
    }
}
