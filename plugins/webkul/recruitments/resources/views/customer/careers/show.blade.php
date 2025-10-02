@extends('recruitments::customer.layouts.app')

@push('styles')
    <style>
        {!! file_get_contents(base_path('plugins/webkul/recruitments/resources/css/careers.css')) !!}
    </style>
@endpush

@section('content')
    <section class="careers-hero rounded-3xl border border-white/40 bg-white/60 px-6 py-10 shadow-sm backdrop-blur sm:px-10">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-4">
                <span class="careers-badge">{{ __('Now hiring') }}</span>
                <h1 class="text-4xl font-semibold tracking-tight text-slate-900 sm:text-5xl">{{ $job->name }}</h1>
                <p class="text-base text-slate-600 sm:text-lg">
                    {{ strip_tags(\Illuminate\Support\Str::limit($job->description, 160)) }}
                </p>
                <div class="flex flex-wrap gap-3 text-sm text-slate-500">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/70 px-3 py-1 shadow-sm">
                        <x-filament::icon icon="heroicon-m-building-office" class="h-4 w-4 text-primary-500" />
                        {{ $job->company?->name ?? __('Our company') }}
                    </span>
                    @if ($job->department)
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/70 px-3 py-1 shadow-sm">
                            <x-filament::icon icon="heroicon-m-users" class="h-4 w-4 text-primary-500" />
                            {{ $job->department->name }}
                        </span>
                    @endif
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/70 px-3 py-1 shadow-sm">
                        <x-filament::icon icon="heroicon-m-briefcase" class="h-4 w-4 text-primary-500" />
                        {{ $job->employmentType?->name ?? __('Not specified') }}
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/70 px-3 py-1 shadow-sm">
                        <x-filament::icon icon="heroicon-m-map-pin" class="h-4 w-4 text-primary-500" />
                        {{ $job->address?->name ?? __('Flexible / Remote') }}
                    </span>
                </div>
            </div>
            <div class="flex w-full max-w-xs flex-col gap-2 rounded-2xl border border-white/60 bg-white/70 p-6 shadow-sm">
                <div class="text-xs uppercase tracking-wide text-slate-400">{{ __('Posted on') }}</div>
                <div class="text-2xl font-semibold text-slate-900">{{ optional($job->created_at)->format('M d, Y') }}</div>
                <div class="mt-2 h-px bg-slate-100"></div>
                <div class="flex items-center gap-3 text-sm text-slate-500">
                    <x-filament::icon icon="heroicon-m-clock" class="h-5 w-5 text-primary-500" />
                    <span>{{ __('Apply in minutes � it only takes a few details to get started.') }}</span>
                </div>
            </div>
        </div>
    </section>

    @if (session('status'))
        <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    @if ($hasApplied)
        <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            {{ __('You have already applied for this position. Submitting the form again will update your existing application.') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mt-10 grid gap-8 lg:grid-cols-[2fr,1fr]">
        <article class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm lg:p-8">
            <div class="space-y-8">
                @if ($job->description)
                    <section class="space-y-3">
                        <h2 class="text-xl font-semibold text-slate-900">{{ __('About the role') }}</h2>
                        <div class="prose max-w-none text-slate-700 prose-li:marker:text-primary-500 dark:prose-invert">
                            {!! $job->description !!}
                        </div>
                    </section>
                @endif

                @if ($job->requirements)
                    <section class="space-y-3">
                        <h2 class="text-xl font-semibold text-slate-900">{{ __('What we are looking for') }}</h2>
                        <div class="prose max-w-none text-slate-700 prose-li:marker:text-primary-500 dark:prose-invert">
                            {!! $job->requirements !!}
                        </div>
                    </section>
                @endif

                <section class="grid gap-4 rounded-2xl bg-slate-50 px-4 py-5 text-sm text-slate-600 sm:grid-cols-2">
                    <div class="space-y-1">
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">{{ __('Openings') }}</p>
                        <p class="font-semibold text-slate-800">{{ $job->no_of_recruitment ?? 'N/A' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">{{ __('Reports to') }}</p>
                        <p class="font-semibold text-slate-800">{{ $job->manager?->name ?? __('To be assigned') }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">{{ __('Recruiter') }}</p>
                        <p class="font-semibold text-slate-800">{{ $job->recruiter?->name ?? __('Team collaboration') }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">{{ __('Availability') }}</p>
                        <p class="font-semibold text-slate-800">{{ optional($job->date_from)->format('M d, Y') ?? __('Immediate') }}</p>
                    </div>
                </section>

                @if ($job->skills?->count())
                    <section class="space-y-3">
                        <h2 class="text-xl font-semibold text-slate-900">{{ __('Key skills & tools') }}</h2>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($job->skills as $skill)
                                <span class="rounded-full bg-primary-50 px-3 py-1 text-xs font-medium text-primary-600">{{ $skill->name }}</span>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>
        </article>

        <aside class="space-y-6">
            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm lg:sticky lg:top-16">
                <h2 class="text-lg font-semibold text-slate-900">{{ __('Apply for this position') }}</h2>

                @guest('customer')
                    <p class="mt-3 text-sm text-slate-500">
                        {{ __('You can apply without an account. Sign in to prefill your details and track your application.') }}
                    </p>
                    <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-3">
                        <a
                            href="{{ filament()->getLoginUrl() }}"
                            class="rounded-md px-3 py-2 text-slate-700 ring-1 ring-transparent transition hover:text-slate-900 focus:outline-none focus-visible:ring-primary-500"
                        >
                            {{ __('Log in') }}
                        </a>

                        <a
                            href="{{ filament()->getRegistrationUrl() }}"
                            class="rounded-md px-3 py-2 text-slate-700 ring-1 ring-transparent transition hover:text-slate-900 focus:outline-none focus-visible:ring-primary-500"
                        >
                            {{ __('Register') }}
                        </a>
                    </div>
                @endguest

                <form action="{{ route('recruitments.careers.apply', $job) }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-6">
                    @csrf

                    <div class="space-y-2">
                        <span class="careers-form-subtitle">{{ __('Step 1 - Personal details') }}</span>
                        <p class="text-sm text-slate-500">{{ __('Share how we can reach you and link to your professional profile.') }}</p>
                    </div>

                    <div class="careers-apply-form">
                        <div class="careers-form-field">
                            <label for="name" class="careers-form-label">{{ __('Full name') }}</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $customer?->name) }}" required class="careers-form-input" />
                            @error('name')
                                <p class="text-xs text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="careers-form-field">
                            <label for="email" class="careers-form-label">{{ __('Email') }}</label>
                            <input id="email" name="email" type="email" value="{{ old('email', $customer?->email) }}" required class="careers-form-input" />
                            @error('email')
                                <p class="text-xs text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="careers-form-field">
                            <label for="phone" class="careers-form-label">{{ __('Phone') }}</label>
                            <input id="phone" name="phone" type="text" value="{{ old('phone', $customer?->phone) }}" class="careers-form-input" />
                            @error('phone')
                                <p class="text-xs text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="careers-form-field">
                            <label for="linkedin_profile" class="careers-form-label">{{ __('LinkedIn profile') }}</label>
                            <input id="linkedin_profile" name="linkedin_profile" type="url" value="{{ old('linkedin_profile', $customer?->website) }}" placeholder="https://linkedin.com/in/username" class="careers-form-input" />
                            @error('linkedin_profile')
                                <p class="text-xs text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="space-y-2">
                        <span class="careers-form-subtitle">{{ __('Step 2 - Supporting details') }}</span>
                        <p class="text-sm text-slate-500">{{ __('Help us understand your availability and share any additional context.') }}</p>
                    </div>

                    <div class="careers-apply-form">
                        <div class="careers-form-field">
                            <label for="availability_date" class="careers-form-label">{{ __('Availability date') }}</label>
                            <input id="availability_date" name="availability_date" type="date" value="{{ old('availability_date') }}" class="careers-form-input" />
                            @error('availability_date')
                                <p class="text-xs text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="careers-form-field">
                            <label for="salary_expectation" class="careers-form-label">{{ __('Salary expectation') }}</label>
                            <input id="salary_expectation" name="salary_expectation" type="number" min="0" step="100000" value="{{ old('salary_expectation') }}" class="careers-form-input" />
                            @error('salary_expectation')
                                <p class="text-xs text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="careers-form-field md:col-span-2">
                            <label for="cover_letter" class="careers-form-label">{{ __('Cover letter / introduction') }}</label>
                            <textarea id="cover_letter" name="cover_letter" rows="4" class="careers-form-textarea">{{ old('cover_letter') }}</textarea>
                            @error('cover_letter')
                                <p class="text-xs text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="careers-form-field md:col-span-2">
                            <label for="notes" class="careers-form-label">{{ __('Additional notes') }}</label>
                            <textarea id="notes" name="notes" rows="3" class="careers-form-textarea">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="text-xs text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="careers-form-field md:col-span-2">
                            <label for="resume" class="careers-form-label">{{ __('Upload CV / resume') }}</label>
                            <input id="resume" name="resume" type="file" accept=".pdf,.doc,.docx" class="careers-form-input" />
                            <p class="careers-form-note">{{ __('Allowed types: PDF, DOC, DOCX. Max 5 MB.') }}</p>
                            @error('resume')
                                <p class="text-xs text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="careers-form-note">{{ __('We aim to respond to every application within three business days.') }}</p>

                        <button type="submit" class="careers-form-action">
                            <x-filament::icon icon="heroicon-m-paper-airplane" class="h-5 w-5" />
                            <span>{{ __('Submit application') }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">{{ __('Need help?') }}</h3>
                <p class="mt-2 text-sm text-slate-500">{{ __('Our recruitment team is here to assist with any questions about the interview process or role requirements.') }}</p>
                <div class="mt-4 space-y-2 text-sm text-slate-600">
                    @if (! empty($contacts['email']))
                        <div class="flex items-center gap-2">
                            <x-filament::icon icon="heroicon-m-envelope" class="h-4 w-4 text-primary-500" />
                            <a href="mailto:{{ $contacts['email'] }}" class="text-primary-600 hover:text-primary-700">{{ $contacts['email'] }}</a>
                        </div>
                    @endif
                    @if (! empty($contacts['phone']))
                        <div class="flex items-center gap-2">
                            <x-filament::icon icon="heroicon-m-phone" class="h-4 w-4 text-primary-500" />
                            <a href="tel:{{ $contacts['phone'] }}" class="text-primary-600 hover:text-primary-700">{{ $contacts['phone'] }}</a>
                        </div>
                    @endif
                </div>
            </div>
        </aside>
    </div>

    @if ($relatedJobs->isNotEmpty())
        <section class="mt-16 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-semibold text-slate-900">{{ __('More opportunities to explore') }}</h2>
                <a href="{{ route('recruitments.careers.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">{{ __('Browse all roles') }}</a>
            </div>
            <div class="grid gap-6 sm:grid-cols-2">
                @foreach ($relatedJobs as $related)
                    <article class="careers-card flex h-full flex-col rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900">
                                    <a href="{{ route('recruitments.careers.show', $related) }}" class="hover:text-primary-600">{{ $related->name }}</a>
                                </h3>
                                <p class="text-xs text-slate-500">{{ $related->company?->name ?? __('Our company') }} � {{ $related->employmentType?->name ?? __('Not specified') }}</p>
                            </div>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs text-slate-500">{{ optional($related->created_at)->diffForHumans(null, true) }}</span>
                        </div>
                        <p class="mt-3 line-clamp-3 text-sm text-slate-600">{{ \Illuminate\Support\Str::limit(strip_tags($related->description), 120) }}</p>
                        <div class="mt-5 flex items-center justify-between text-xs text-slate-400">
                            <span class="inline-flex items-center gap-1">
                                <x-filament::icon icon="heroicon-m-map-pin" class="h-4 w-4" />
                                {{ $related->address?->name ?? __('Flexible / Remote') }}
                            </span>
                            <a href="{{ route('recruitments.careers.show', $related) }}" class="inline-flex items-center gap-1 text-primary-600 hover:text-primary-700">
                                {{ __('View role') }}
                                <x-filament::icon icon="heroicon-m-arrow-up-right" class="h-4 w-4" />
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
@endsection
