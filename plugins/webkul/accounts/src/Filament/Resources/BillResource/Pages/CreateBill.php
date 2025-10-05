<?php

namespace Webkul\Account\Filament\Resources\BillResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Facades\Account;
use Webkul\Account\Filament\Resources\BillResource;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\Payment;

class CreateBill extends CreateRecord
{
    protected static string $resource = BillResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('accounts::filament/resources/bill/pages/create-bill.notification.title'))
            ->body(__('accounts::filament/resources/bill/pages/create-bill.notification.body'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['move_type'] ??= MoveType::IN_INVOICE;

        $data['date'] = now();

        // Auto-generate Bill Reference if empty
        if (empty($data['reference'])) {
            $data['reference'] = $this->generateBillReference();
        }

        // Auto-generate Payment Reference on bill if empty
        if (empty($data['payment_reference'])) {
            $data['payment_reference'] = $this->generatePaymentReferenceForMoves();
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        Account::computeAccountMove($this->getRecord());
    }

    private function generateBillReference(): string
    {
        $prefix = 'BILL-'.now()->format('Ymd').'-';

        $last = Move::query()
            ->where('reference', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->value('reference');

        $next = 1;
        if ($last && Str::startsWith($last, $prefix)) {
            $suffix = (int) Str::after($last, $prefix);
            $next = $suffix + 1;
        }

        return $prefix.str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }

    private function generatePaymentReferenceForMoves(): string
    {
        $prefix = 'PAY-'.now()->format('Ymd').'-';

        $last = Move::query()
            ->where('payment_reference', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->value('payment_reference');

        $next = 1;
        if ($last && Str::startsWith($last, $prefix)) {
            $suffix = (int) Str::after($last, $prefix);
            $next = $suffix + 1;
        }

        return $prefix.str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }
}
