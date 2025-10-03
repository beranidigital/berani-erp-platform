@extends('recruitments::customer.layouts.app')

@push('styles')
    <style>
        {!! file_get_contents(base_path('plugins/webkul/recruitments/resources/css/careers.css')) !!}
    </style>
@endpush

@section('content')
    <section class="careers-hero rounded-3xl border border-white/40 bg-white/60 px-6 py-10 shadow-sm backdrop-blur sm:px-10">
        <div class="max-w-3xl space-y-4">
            <span class="careers-badge">{{ __('We are growing') }}</span>
            <h1 class="text-4xl font-semibold tracking-tight text-slate-900 sm:text-5xl">
                {{ __('Where ambition meets opportunity') }}
            </h1>
            <p class="text-base text-slate-600 sm:text-lg">
                {{ __('Join a team that values curiosity, collaboration, and building meaningful products. Explore open positions and find the role that fits your next big move.') }}
            </p>
        </div>

        <dl class="mt-8 grid grid-cols-1 gap-4 text-sm sm:grid-cols-3">
            <div class="rounded-2xl border border-white/60 bg-white/70 p-5 shadow-sm">
                <dt class="text-slate-500">{{ __('Open roles worldwide') }}</dt>
                <dd class="mt-2 text-3xl font-semibold text-slate-900">{{ $stats['openRoles'] }}</dd>
                <p class="mt-1 text-xs text-slate-400">{{ __('Across product, operations, and customer teams') }}</p>
            </div>
            <div class="rounded-2xl border border-white/60 bg-white/70 p-5 shadow-sm">
                <dt class="text-slate-500">{{ __('New this week') }}</dt>
                <dd class="mt-2 text-3xl font-semibold text-slate-900">{{ $stats['newThisWeek'] }}</dd>
                <p class="mt-1 text-xs text-slate-400">{{ __('Fresh opportunities added in the last 7 days') }}</p>
            </div>
            <div class="rounded-2xl border border-white/60 bg-white/70 p-5 shadow-sm">
                <dt class="text-slate-500">{{ __('Remote-friendly openings') }}</dt>
                <dd class="mt-2 text-3xl font-semibold text-slate-900">{{ $stats['remoteFriendly'] }}</dd>
                <p class="mt-1 text-xs text-slate-400">{{ __('Roles that support hybrid or remote work') }}</p>
            </div>
        </dl>
    </section>

    <div class="mt-8 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm sm:p-8">
        <form method="GET" id="careers-filter-form" class="space-y-6">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="flex flex-col gap-2">
                    <label for="keyword" class="text-sm font-medium text-slate-600">{{ __('Keyword') }}</label>
                    <input
                        id="keyword"
                        type="search"
                        name="search"
                        value="{{ $filters['search'] }}"
                        placeholder="{{ __('Search by title, skills, or keywords') }}"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200"
                    >
                </div>

                <div class="flex flex-col gap-2">
                    <label for="company" class="text-sm font-medium text-slate-600">{{ __('Company') }}</label>
                    <select
                        id="company"
                        name="company"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200"
                    >
                        <option value="">{{ __('All companies') }}</option>
                        @foreach ($filterOptions['companies'] as $value => $label)
                            <option value="{{ $value }}" @selected($filters['company'] == $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="department" class="text-sm font-medium text-slate-600">{{ __('Department') }}</label>
                    <select
                        id="department"
                        name="department"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200"
                    >
                        <option value="">{{ __('All departments') }}</option>
                        @foreach ($filterOptions['departments'] as $value => $label)
                            <option value="{{ $value }}" @selected($filters['department'] == $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="employment_type" class="text-sm font-medium text-slate-600">{{ __('Employment type') }}</label>
                    <select
                        id="employment_type"
                        name="employment_type"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200"
                    >
                        <option value="">{{ __('All types') }}</option>
                        @foreach ($filterOptions['employmentTypes'] as $value => $label)
                            <option value="{{ $value }}" @selected($filters['employment_type'] == $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="location" class="text-sm font-medium text-slate-600">{{ __('Location') }}</label>
                    <select
                        id="location"
                        name="location"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200"
                    >
                        <option value="">{{ __('All locations') }}</option>
                        @foreach ($filterOptions['locations'] as $value => $label)
                            <option value="{{ $value }}" @selected($filters['location'] == $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="sort" class="text-sm font-medium text-slate-600">{{ __('Sort by') }}</label>
                    <select
                        id="sort"
                        name="sort"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200"
                    >
                        @foreach ($filterOptions['sort'] as $value => $label)
                            <option value="{{ $value }}" @selected($filters['sort'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <x-filament::icon icon="heroicon-o-light-bulb" class="h-4 w-4" />
                    <span>{{ __('Tip: combine keyword + filters to narrow down to the ideal role.') }}</span>
                </div>

                <div class="flex items-center gap-3">
                    @if ($hasActiveFilters || filled($filters['search']))
                        <a
                            href="{{ route('recruitments.careers.index') }}"
                            class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-500 hover:border-primary-200 hover:text-primary-600"
                        >
                            {{ __('Reset filters') }}
                        </a>
                    @endif

                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-200"
                    >
                        <x-filament::icon icon="heroicon-m-funnel" class="h-4 w-4" />
                        {{ __('Apply filters') }}
                    </button>
                </div>
            </div>
        </form>

        @if ($hasActiveFilters || filled($filters['search']))
            <div class="mt-6 flex flex-wrap items-center gap-2">
                <span class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ __('Active filters') }}:</span>
                @if ($filters['search'])
                    <span class="inline-flex items-center gap-1 rounded-full bg-primary-50 px-3 py-1 text-xs font-medium text-primary-600">
                        <x-filament::icon icon="heroicon-m-magnifying-glass" class="h-4 w-4" />
                        {{ __('Keyword') }}: "{{ $filters['search'] }}"
                    </span>
                @endif
                @foreach ($activeFilters as $key => $value)
                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                        <x-filament::icon icon="heroicon-m-adjustments-horizontal" class="h-4 w-4" />
                        {{ $value }}
                    </span>
                @endforeach
            </div>
        @endif
    </div>

    <div class="mt-10 grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($jobs as $job)
            @php
                $isNew = optional($job->created_at)->greaterThanOrEqualTo(now()->subDays(10));
            @endphp
            <article class="careers-card flex h-full flex-col rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div class="space-y-1">
                        <h2 class="text-xl font-semibold text-slate-900">
                            <a href="{{ route('recruitments.careers.show', $job) }}" class="hover:text-primary-600">
                                {{ $job->name }}
                            </a>
                        </h2>
                        <p class="text-sm text-slate-500">
                            {{ $job->company?->name ?? __('Our company') }}
                            @if ($job->department)
                                � {{ $job->department->name }}
                            @endif
                        </p>
                    </div>
                    @if ($isNew)
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-600">
                            <x-filament::icon icon="heroicon-m-sparkles" class="h-4 w-4" />
                            {{ __('New') }}
                        </span>
                    @endif
                </div>

                <p class="mt-4 line-clamp-3 text-sm leading-relaxed text-slate-600">
                    {{ \Illuminate\Support\Str::limit(strip_tags($job->description), 150) }}
                </p>

                <dl class="mt-5 grid grid-cols-2 gap-3 text-xs text-slate-500">
                    <div class="space-y-1">
                        <dt class="font-medium text-slate-400">{{ __('Employment') }}</dt>
                        <dd class="text-slate-600">{{ $job->employmentType?->name ?? __('Not specified') }}</dd>
                    </div>
                    <div class="space-y-1">
                        <dt class="font-medium text-slate-400">{{ __('Openings') }}</dt>
                        <dd class="text-slate-600">{{ $job->no_of_recruitment ?? 'N/A' }}</dd>
                    </div>
                    <div class="space-y-1">
                        <dt class="font-medium text-slate-400">{{ __('Location') }}</dt>
                        <dd class="text-slate-600">{{ $job->address?->name ?? __('Flexible / Remote') }}</dd>
                    </div>
                    <div class="space-y-1">
                        <dt class="font-medium text-slate-400">{{ __('Posted') }}</dt>
                        <dd class="text-slate-600">{{ optional($job->created_at)->format('M d, Y') }}</dd>
                    </div>
                    @if($job->manager)
                    <div class="space-y-1">
                        <dt class="font-medium text-slate-400">{{ __('Reports to') }}</dt>
                        <dd class="text-slate-600">{{ $job->manager->name }}</dd>
                    </div>
                    @endif
                    @if($job->date_from)
                    <div class="space-y-1">
                        <dt class="font-medium text-slate-400">{{ __('Start date') }}</dt>
                        <dd class="text-slate-600">{{ $job->date_from->format('M d, Y') }}</dd>
                    </div>
                    @endif
                </dl>

                <div class="mt-6 flex items-center justify-between gap-3">
                    <div class="flex flex-wrap gap-2 text-xs text-slate-400">
                        @foreach ($job->skills?->take(3) ?? [] as $skill)
                            <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-slate-500">{{ $skill->name }}</span>
                        @endforeach
                    </div>
                    <a
                        href="{{ route('recruitments.careers.show', $job) }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-primary-50 px-4 py-2 text-sm font-medium text-primary-600 transition hover:bg-primary-100"
                    >
                        {{ __('View & apply') }}
                        <x-filament::icon icon="heroicon-m-arrow-right" class="h-4 w-4" />
                    </a>
                </div>
            </article>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-200 bg-white px-8 py-14 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-primary-100 text-primary-600">
                    <x-filament::icon icon="heroicon-m-magnifying-glass-circle" class="h-7 w-7" />
                </div>
                <h3 class="mt-4 text-xl font-semibold text-slate-800">{{ __('No roles match your filters just yet') }}</h3>
                <p class="mt-2 text-sm text-slate-500">{{ __('Try adjusting your filters or check back soon�we update openings every week.') }}</p>
                <a href="{{ route('recruitments.careers.index') }}" class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-primary-600 hover:text-primary-700">
                    <x-filament::icon icon="heroicon-m-arrow-path" class="h-4 w-4" />
                    {{ __('Clear filters and view all roles') }}
                </a>
            </div>
        @endforelse
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const filterForm = document.getElementById('careers-filter-form');

            if (!filterForm) {
                return;
            }

            const submitForm = () => {
                window.requestAnimationFrame(() => filterForm.requestSubmit());
            };

            filterForm.querySelectorAll('select').forEach((selectEl) => {
                selectEl.addEventListener('change', submitForm, { passive: true });
            });

            const searchInput = filterForm.querySelector('input[name="search"]');

            if (searchInput) {
                let debounceTimer;

                const triggerSearch = () => {
                    window.clearTimeout(debounceTimer);

                    debounceTimer = window.setTimeout(() => {
                        submitForm();
                    }, 400);
                };

                searchInput.addEventListener('input', triggerSearch);
                searchInput.addEventListener('search', submitForm);
            }
        });
    </script>
@endpush
