<?php

namespace Webkul\Blog\Filament\Customer\Resources\PostResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Blog\Filament\Customer\Resources\PostResource;

class ViewPost extends ViewRecord
{
    protected static string $resource = PostResource::class;

    protected string $view = 'blogs::filament.customer.resources.post.pages.view-record';

    public function mountParentRecord(): void
    {
        if ($this->parentRecord) {
            return;
        }

        $parentResourceRegistration = static::getResource()::getParentResourceRegistration();

        if (! $parentResourceRegistration) {
            return;
        }

        $parameters = request()->route()->parameters();
        $paramName = $parentResourceRegistration->getParentRouteParameterName();

        // If the parent route parameter (e.g., category) is missing or null,
        // skip resolving the parent record to avoid passing null to Filament.
        if (! array_key_exists($paramName, $parameters) || $parameters[$paramName] === null) {
            return;
        }

        $this->parentRecord = $this->resolveParentRecord($parameters);

        $this->authorizeParentRecordAccess();
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->title;
    }
}
