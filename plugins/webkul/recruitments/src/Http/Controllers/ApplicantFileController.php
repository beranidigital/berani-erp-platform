<?php

namespace Webkul\Recruitment\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Webkul\Recruitment\Models\Applicant;

class ApplicantFileController
{
    public function resume(Request $request, Applicant $applicant): StreamedResponse
    {
        Gate::authorize('view', $applicant);

        $props = $applicant->applicant_properties ?? [];

        $path = $props['resume_path'] ?? null;
        $name = $props['resume_original_name'] ?? basename((string) $path);

        abort_unless($path && Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->download($path, $name);
    }
}

