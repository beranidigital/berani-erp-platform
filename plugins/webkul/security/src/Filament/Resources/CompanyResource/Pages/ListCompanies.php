<?php

namespace Webkul\Security\Filament\Resources\CompanyResource\Pages;

use Filament\Actions\CreateAction;
use Webkul\Support\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Webkul\TableViews\Filament\Concerns\HasTableViews;
use Webkul\Security\Filament\Resources\CompanyResource;

class ListCompanies extends ListRecords
{
    use HasTableViews;

    protected static string $resource = CompanyResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('security::filament/resources/company/pages/list-company.tabs.all'))
                ->badge(Company::count()),
            'archived' => Tab::make(__('security::filament/resources/company/pages/list-company.tabs.archived'))
                ->badge(Company::onlyTrashed()->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->onlyTrashed())
        ];
    }

    protected function getTableQuery(): Builder
{
    $query = parent::getTableQuery();

    if ($this->activeTab === 'archived') {
        return $query->onlyTrashed();
    }

    return $query;
}



    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->icon('heroicon-o-plus-circle')
                ->label(__('security::filament/resources/company/pages/list-company.header-actions.create.label')),
        ];
    }
}
