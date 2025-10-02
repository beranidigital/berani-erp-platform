<?php

namespace Webkul\Recruitment\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Filament\Facades\Filament;
use Webkul\Recruitment\Http\Requests\StoreJobApplicationRequest;
use Webkul\Recruitment\Models\Applicant;
use Webkul\Recruitment\Models\Candidate;
use Webkul\Recruitment\Models\JobPosition;
use Webkul\Recruitment\Models\Stage;
use Webkul\Security\Models\User;
use Webkul\Website\Settings\ContactSettings;
use Webkul\Employee\Models\Department;
use Webkul\Employee\Models\EmploymentType;
use Webkul\Support\Models\Company;
use Webkul\Partner\Models\Partner;

class CustomerJobController
{
    public function index(Request $request): View
    {
        $filters = [
            'search'           => (string) $request->input('search', ''),
            'company'          => $request->input('company'),
            'department'       => $request->input('department'),
            'employment_type'  => $request->input('employment_type'),
            'location'         => $request->input('location'),
            'sort'             => $request->input('sort', 'latest'),
        ];

        $jobsQuery = JobPosition::query()
            ->with(['department', 'company', 'employmentType', 'address', 'manager', 'skills'])
            ->where('is_active', true)
            ->when(filled($filters['search']), function ($query) use ($filters) {
                $search = trim($filters['search']);

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('requirements', 'like', "%{$search}%");
                });
            })
            ->when(filled($filters['company']), fn ($q) => $q->where('company_id', $filters['company']))
            ->when(filled($filters['department']), fn ($q) => $q->where('department_id', $filters['department']))
            ->when(filled($filters['employment_type']), fn ($q) => $q->where('employment_type_id', $filters['employment_type']))
            ->when(filled($filters['location']), fn ($q) => $q->where('address_id', $filters['location']));

        $jobsQuery = match ($filters['sort']) {
            'oldest'     => $jobsQuery->orderBy('created_at'),
            'name_asc'   => $jobsQuery->orderBy('name'),
            'name_desc'  => $jobsQuery->orderByDesc('name'),
            default      => $jobsQuery->orderByDesc('created_at'),
        };

        $jobs = $jobsQuery->get(); // Get all jobs without pagination

        // Stats for hero cards
        $baseStatsQuery = JobPosition::query()->where('is_active', true);
        $stats = [
            'openRoles'     => (clone $baseStatsQuery)->count(),
            'newThisWeek'   => (clone $baseStatsQuery)->where('created_at', '>=', Carbon::now()->subDays(7))->count(),
            'remoteFriendly'=> (clone $baseStatsQuery)->whereNull('address_id')->count(),
        ];

        // Filter options
        $filterOptions = [
            'companies'        => Company::query()->orderBy('name')->pluck('name', 'id'),
            'departments'      => Department::query()->orderBy('name')->pluck('name', 'id'),
            'employmentTypes'  => EmploymentType::query()->orderBy('name')->pluck('name', 'id'),
            'locations'        => Partner::query()->where('sub_type', 'company')->orderBy('name')->pluck('name', 'id'),
            'sort'             => [
                'latest'    => __('Latest'),
                'oldest'    => __('Oldest'),
                'name_asc'  => __('Name A–Z'),
                'name_desc' => __('Name Z–A'),
            ],
        ];

        // Active filters labels (basic for now)
        $activeFilters = [];
        if (filled($filters['company'])) {
            $activeFilters['company'] = $filterOptions['companies'][$filters['company']] ?? __('Company');
        }
        if (filled($filters['department'])) {
            $activeFilters['department'] = $filterOptions['departments'][$filters['department']] ?? __('Department');
        }
        if (filled($filters['employment_type'])) {
            $activeFilters['employment_type'] = $filterOptions['employmentTypes'][$filters['employment_type']] ?? __('Employment type');
        }
        if (filled($filters['location'])) {
            $activeFilters['location'] = $filterOptions['locations'][$filters['location']] ?? __('Location');
        }

        $hasActiveFilters = ! empty($activeFilters);

        $customer = auth('customer')->user();

        Filament::setCurrentPanel('customer');

        return view('recruitments::customer.careers.index', [
            'jobs'            => $jobs,
            'customer'        => $customer,
            'contacts'        => $this->getContacts(),
            'socialLinks'     => $this->getSocialLinks(),
            'title'           => __('Careers'),
            'stats'           => $stats,
            'filters'         => $filters,
            'filterOptions'   => $filterOptions,
            'activeFilters'   => $activeFilters,
            'hasActiveFilters'=> $hasActiveFilters,
        ]);
    }

    public function show(JobPosition $jobPosition): View
    {
        abort_if(! $jobPosition->is_active, 404);

        $jobPosition->loadMissing(['department', 'company', 'employmentType', 'skills']);

        // Prepare related jobs (same department or same company), exclude current
        $relatedJobs = JobPosition::query()
            ->with(['company', 'employmentType', 'address'])
            ->where('is_active', true)
            ->where('id', '!=', $jobPosition->id)
            ->when($jobPosition->department_id, function ($q) use ($jobPosition) {
                $q->where('department_id', $jobPosition->department_id);
            }, function ($q) use ($jobPosition) {
                if ($jobPosition->company_id) {
                    $q->where('company_id', $jobPosition->company_id);
                }
            })
            ->latest('created_at')
            ->limit(4)
            ->get();

        $customer = auth('customer')->user();

        $hasApplied = false;

        if ($customer) {
            $hasApplied = $jobPosition->applications()
                ->whereHas('candidate', function ($query) use ($customer) {
                    $query->where('partner_id', $customer->id)
                        ->orWhere('email_from', $customer->email);
                })
                ->exists();
        }

        Filament::setCurrentPanel('customer');

        return view('recruitments::customer.careers.show', [
            'job'         => $jobPosition,
            'customer'    => $customer,
            'hasApplied'  => $hasApplied,
            'contacts'    => $this->getContacts(),
            'socialLinks' => $this->getSocialLinks(),
            'title'       => $jobPosition->name,
            'relatedJobs' => $relatedJobs,
        ]);
    }

    public function apply(StoreJobApplicationRequest $request, JobPosition $jobPosition): RedirectResponse
    {
        try {
            \Log::info('Job application received', [
                'job_position_id' => $jobPosition->id,
                'customer_authenticated' => auth('customer')->check(),
                'request_data' => $request->all()
            ]);

            abort_if(! $jobPosition->is_active, 404);

            $validated = $request->validated();
            $customer = auth('customer')->user();

            $resumePath = null;
            $resumeOriginalName = null;

            if ($request->hasFile('resume')) {
                $resumeOriginalName = $request->file('resume')->getClientOriginalName();
                $resumePath = $request->file('resume')->store('recruitments/resumes', 'public');
            }

            $isNewApplication = false;

            $fallbackCreatorId = $jobPosition->recruiter_id ?? User::query()->first()?->id ?? 1; // Use first user or default to ID 1

            DB::transaction(function () use ($validated, $customer, $jobPosition, $resumePath, $resumeOriginalName, &$isNewApplication, $fallbackCreatorId) {
                $candidate = Candidate::withTrashed()->firstOrNew([
                    'email_from' => $validated['email'],
                ]);

                if ($candidate->trashed()) {
                    $candidate->restore();
                }

                $candidateProperties = $candidate->candidate_properties ?? [];

                if ($resumePath) {
                    $candidateProperties['resume_path'] = $resumePath;
                    $candidateProperties['resume_original_name'] = $resumeOriginalName;
                }

                if (! empty($validated['cover_letter'])) {
                    $candidateProperties['cover_letter'] = $validated['cover_letter'];
                }

                if (! empty($validated['salary_expectation'])) {
                    $candidateProperties['salary_expectation'] = $validated['salary_expectation'];
                }

                // Make sure creator_id is properly set before saving to prevent foreign key constraint errors
                // When a customer submits an application, we should use the recruiter or a fallback user as creator
                $creator_id = $candidate->creator_id ?? $jobPosition->recruiter_id ?? $fallbackCreatorId;
                
                $candidate->fill([
                    'name'              => $validated['name'],
                    'phone'             => $validated['phone'] ?? $customer?->phone,
                    'linkedin_profile'  => $validated['linkedin_profile'] ?? null,
                    'availability_date' => $validated['availability_date']
                        ? Carbon::parse($validated['availability_date'])
                        : null,
                    'company_id'        => $customer?->company_id ?? $jobPosition->company_id,
                    'partner_id'        => $customer?->id ?? $candidate->partner_id,
                    'creator_id'        => $creator_id,
                    'is_active'         => true,
                ]);

                if ($customer && ! $candidate->partner_id) {
                    $candidate->partner_id = $customer->id;
                }

                $candidate->candidate_properties = array_filter(
                    $candidateProperties,
                    fn ($value) => ! is_null($value) && $value !== ''
                );

                // Save the candidate without triggering events to prevent automatic partner creation
                // which can cause foreign key constraint errors
                $candidate->saveQuietly();

                $stage = Stage::query()->where('is_default', true)->orderBy('sort')->first()
                    ?? Stage::query()->orderBy('sort')->first();

                $applicant = Applicant::firstOrNew([
                    'candidate_id' => $candidate->id,
                    'job_id'       => $jobPosition->id,
                ]);

                if (! $applicant->exists) {
                    $isNewApplication = true;
                }

                $applicantProperties = $applicant->applicant_properties ?? [];

                if ($resumePath) {
                    $applicantProperties['resume_path'] = $resumePath;
                    $applicantProperties['resume_original_name'] = $resumeOriginalName;
                }

                if (! empty($validated['cover_letter'])) {
                    $applicantProperties['cover_letter'] = $validated['cover_letter'];
                }

                // Make sure creator_id is properly set before saving
                $applicant_creator_id = $applicant->creator_id ?? $fallbackCreatorId;
                
                $applicant->fill([
                    'company_id'       => $jobPosition->company_id,
                    'department_id'    => $jobPosition->department_id,
                    'recruiter_id'     => $jobPosition->recruiter_id ?? $fallbackCreatorId,
                    'creator_id'       => $applicant_creator_id,
                    'stage_id'         => $applicant->exists ? $applicant->stage_id : $stage?->id,
                    'last_stage_id'    => $applicant->exists ? $applicant->last_stage_id : $stage?->id,
                    'applicant_notes'  => $validated['notes'] ?? $applicant->applicant_notes,
                    'salary_expected'  => $validated['salary_expectation'] ?? $applicant->salary_expected,
                    'is_active'        => true,
                    'state'            => $applicant->state ?? \Webkul\Recruitment\Enums\RecruitmentState::NORMAL->value,
                ]);

                if (! $applicant->create_date) {
                    $now = Carbon::now();
                    $applicant->create_date = $now;
                    $applicant->date_opened = $now;
                }

                $applicant->applicant_properties = array_filter(
                    $applicantProperties,
                    fn ($value) => ! is_null($value) && $value !== ''
                );

                // Temporarily disable activity logging if needed
                $hasLogActivity = in_array(\Webkul\Chatter\Traits\HasLogActivity::class, class_uses($applicant));
                if ($hasLogActivity) {
                    $applicant::unsetEventDispatcher();
                }
                
                $applicant->save();

                // Re-enable event dispatching if it was disabled
                if ($hasLogActivity) {
                    $applicant::setEventDispatcher(\Illuminate\Support\Facades\Event::getFacadeRoot());
                }

                \Log::info('Application saved successfully', [
                    'candidate_id' => $candidate->id,
                    'applicant_id' => $applicant->id,
                    'is_new_application' => $isNewApplication
                ]);

                // Send notification to admin/recruiter about the new application
                if ($isNewApplication) {
                    try {
                        // Send application confirmation email to the candidate
                        $candidateEmailSent = app(\Webkul\Support\Services\EmailService::class)->send(
                            view: 'recruitments::mails.application-confirm',
                            mailClass: \Webkul\Recruitment\Mail\ApplicationConfirmMail::class,
                            payload: [
                                'to' => [
                                    'address' => $candidate->email_from,
                                    'name' => $candidate->name,
                                ],
                                'subject' => __('Application Received for :job', ['job' => $jobPosition->name]),
                                'record_name' => $candidate->name,
                                'job_position' => $jobPosition->name,
                                'from' => [
                                    'name' => config('app.name'),
                                    'address' => config('mail.from.address'),
                                    'company' => [
                                        'name' => config('app.name'),
                                        'email' => config('mail.from.address'),
                                        'phone' => config('app.phone', 'N/A')
                                    ]
                                ]
                            ]
                        );

                        if (!$candidateEmailSent) {
                            \Log::warning('Failed to send application confirmation email to candidate', [
                                'candidate_email' => $candidate->email_from,
                                'candidate_name' => $candidate->name,
                                'job_position' => $jobPosition->name
                            ]);
                        }

                        // Send notification to the recruiter/admin about the new application
                        $recruiter = $jobPosition->recruiter;
                        if ($recruiter) {
                            $recruiterEmailSent = app(\Webkul\Support\Services\EmailService::class)->send(
                                view: 'recruitments::mails.application-confirm',
                                mailClass: \Webkul\Recruitment\Mail\ApplicationConfirmMail::class,
                                payload: [
                                    'to' => [
                                        'address' => $recruiter->email,
                                        'name' => $recruiter->name,
                                    ],
                                    'subject' => __('New Application for :job - :candidate', [
                                        'job' => $jobPosition->name,
                                        'candidate' => $candidate->name
                                    ]),
                                    'record_name' => $candidate->name . ' applied for ' . $jobPosition->name,
                                    'job_position' => $jobPosition->name,
                                    'from' => [
                                        'name' => config('app.name'),
                                        'address' => config('mail.from.address'),
                                        'company' => [
                                            'name' => config('app.name'),
                                            'email' => config('mail.from.address'),
                                            'phone' => config('app.phone', 'N/A')
                                        ]
                                    ]
                                ]
                            );

                            if (!$recruiterEmailSent) {
                                \Log::warning('Failed to send application notification email to recruiter', [
                                    'recruiter_email' => $recruiter->email,
                                    'recruiter_name' => $recruiter->name,
                                    'candidate_name' => $candidate->name,
                                    'job_position' => $jobPosition->name
                                ]);
                            }
                        }
                    } catch (\Exception $e) {
                        // Log the error but continue with the application process
                        \Log::error('Error sending application emails: ' . $e->getMessage(), [
                            'candidate_id' => $candidate->id,
                            'job_position_id' => $jobPosition->id,
                            'trace' => $e->getTraceAsString()
                        ]);
                        
                        // Even if email fails, we want to continue showing success to the user
                        // since the application data has been saved successfully
                    }
                }
            });

            $message = $isNewApplication
                ? __('Thanks! Your application has been submitted.')
                : __('We have updated your existing application with the latest details.');

            \Log::info('Redirect after successful application', [
                'job_position_id' => $jobPosition->id,
                'message' => $message
            ]);

            return redirect()
                ->route('recruitments.careers.show', $jobPosition)
                ->with('status', $message);
        } catch (\Exception $e) {
            \Log::error('Error during job application process: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // Redirect back with an error message
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['general' => __('An error occurred while processing your application. Please try again.')]);
        }
    }

    private function getContacts(): array
    {
        $contacts = [];

        $contactSettings = app(ContactSettings::class);

        if ($contactSettings->email) {
            $contacts['email'] = $contactSettings->email;
        }

        if ($contactSettings->phone) {
            $contacts['phone'] = $contactSettings->phone;
        }

        return $contacts;
    }

    private function getSocialLinks(): array
    {
        $links = [];

        $contactSettings = app(ContactSettings::class);

        $mapping = [
            'facebook' => fn ($value) => [
                'label' => 'Facebook',
                'url'   => 'https://facebook.com/'.$value,
                'icon'  => '<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"></path></svg>',
            ],
            'twitter' => fn ($value) => [
                'label' => 'Twitter',
                'url'   => 'https://twitter.com/'.$value,
                'icon'  => '<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"></path></svg>',
            ],
            'instagram' => fn ($value) => [
                'label' => 'Instagram',
                'url'   => 'https://instagram.com/'.$value,
                'icon'  => '<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm5.888 14.12c-.23.007-.461.007-.691.007-1.28 0-2.561-.137-3.779-.407-1.325-.296-2.604-.854-3.686-1.674a8.472 8.472 0 01-2.307-2.64 8.081 8.081 0 01-1.174-3.05 9.52 9.52 0 01-.07-2.301c.072-.83.283-1.653.631-2.404a7.63 7.63 0 011.922-2.416A8.57 8.57 0 0111.55 2.21a9.98 9.98 0 012.5-.252c.83.039 1.648.195 2.432.457a8.89 8.89 0 012.896 1.491c1.527 1.186 2.755 2.682 3.375 4.58.418 1.23.57 2.57.445 3.878-.118 1.318-.51 2.575-1.153 3.646-.757 1.255-1.76 2.255-2.92 2.996-.823.497-1.75.778-2.695.897-.258.033-.517.05-.777.05-.258 0-.516-.017-.775-.05zm.705-13.45a7.29 7.29 0 00-3.89-.607c-1.596.178-3.137.981-4.297 2.175a7.185 7.185 0 00-1.88 3.22 7.587 7.587 0 00-.107 2.79c.16 1.3.703 2.527 1.546 3.525.705.831 1.625 1.474 2.648 1.845.772.281 1.596.402 2.408.344 1.1-.077 2.143-.51 2.98-1.196a6.423 6.423 0 001.91-2.626c.394-.92.576-1.947.52-2.962a6.332 6.332 0 00-.709-2.61 6.822 6.822 0 00-1.13-1.701z"></path></svg>',
            ],
            'linkedin' => fn ($value) => [
                'label' => 'LinkedIn',
                'url'   => 'https://linkedin.com/in/'.$value,
                'icon'  => '<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452H17.24v-5.569c0-1.328-.026-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667h-3.21V9h3.079v1.561h.044c.429-.81 1.475-1.666 3.037-1.666 3.25 0 3.852 2.14 3.852 4.926v6.631zM5.337 7.433a1.861 1.861 0 01-1.854-1.867c0-1.029.83-1.867 1.854-1.867s1.853.838 1.853 1.867c0 1.029-.829 1.867-1.853 1.867zM7.119 20.452H3.553V9h3.566v11.452zM22.225 0H1.771C.792 0 0 .771 0 1.723v20.555C0 23.23.792 24 1.771 24h20.451C23.2 24 24 23.23 24 22.278V1.723C24 .77 23.2 0 22.222 0z"></path></svg>',
            ],
        ];

        foreach ($mapping as $field => $callback) {
            if ($value = $contactSettings->{$field}) {
                $links[] = $callback($value);
            }
        }

        return $links;
    }

}


