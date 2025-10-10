<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Infolist;
use Webkul\Account\Filament\Resources\PaymentsResource as BasePaymentsResource;
use Webkul\Invoice\Filament\Clusters\Vendors;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\PaymentsResource\Pages\CreatePayments;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\PaymentsResource\Pages\EditPayments;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\PaymentsResource\Pages\ListPayments;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\PaymentsResource\Pages\ViewPayments;
use Webkul\Invoice\Models\Payment;

class PaymentsResource extends BasePaymentsResource
{
    protected static ?string $model = Payment::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 3;

    protected static ?string $cluster = Vendors::class;

    public static function getModelLabel(): string
    {
        return __('invoices::filament/clusters/vendors/resources/payment.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/vendors/resources/payment.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    public static function form(Schema $schema): Schema
    {
        $form = parent::form($schema);

        $components = $form->getComponents();

        $group = $components[1]?->getChildComponents()[0] ?? null;

        if ($group) {
            $fields = $group->getChildComponents();

            $fields[1] = $fields[1]->label(__('invoices::filament/resources/payment.form.sections.fields.vender-bank-account'));

            $fields[2] = Forms\Components\Select::make('partner_id')
                ->label(__('invoices::filament/resources/payment.form.sections.fields.vender'))
                ->relationship(
                    'partner',
                    'name',
                    fn ($query) => $query->where('sub_type', 'supplier')->orderBy('id')
                )
                ->searchable()
                ->preload();

            // Ensure Payment Method dropdown shows only unique names
            $fields[3] = Forms\Components\Select::make('payment_method_line_id')
                ->label(__('accounts::filament/resources/payment.form.sections.fields.payment-method'))
                ->relationship(
                    'paymentMethodLine',
                    'name',
                    modifyQueryUsing: function (Builder $query) {
                        $query->selectRaw('MIN(accounts_payment_method_lines.id) as id, name')
                            ->groupBy('name');
                    }
                )
                ->getOptionLabelFromRecordUsing(fn ($record): string => $record->name)
                ->searchable()
                ->preload();

            $group->childComponents($fields);
            $components[1]->childComponents([$group]);
        }

        return $form->components($components);
    }

    public static function infolist(Schema $schema): Schema
    {
        $infolist = parent::infolist($schema);

        $components = $infolist->getComponents();

        $group = $components[0]?->getChildComponents()[0] ?? null;

        if ($group) {
            $fields = $group->getChildComponents();

            $fields[2] = $fields[2]->label(__('invoices::filament/resources/payment.form.sections.fields.vender-bank-account'));
            $fields[3] = $fields[3]->label(__('invoices::filament/resources/payment.form.sections.fields.vender'));

            $group->childComponents($fields);
            $components[0]->childComponents([$group]);
        }

        return $infolist->components($components);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPayments::route('/'),
            'create' => CreatePayments::route('/create'),
            'view'   => ViewPayments::route('/{record}'),
            'edit'   => EditPayments::route('/{record}/edit'),
        ];
    }
}
