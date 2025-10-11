<?php

use Illuminate\Support\Facades\Route;
use Webkul\Recruitment\Http\Controllers\ApplicantFileController;
use Webkul\Recruitment\Http\Controllers\CustomerJobController;
use Webkul\Support\Package;

if (! Package::isPluginInstalled('recruitments')) {
    return;
}

$plugin = Package::getPackagePlugin('recruitments');

if (! $plugin || ! $plugin->is_active) {
    return;
}

Route::middleware(['web'])->group(function () {
    Route::get('/careers', [CustomerJobController::class, 'index'])
        ->name('recruitments.careers.index');

    Route::get('/careers/{jobPosition}', [CustomerJobController::class, 'show'])
        ->whereNumber('jobPosition')
        ->name('recruitments.careers.show');

    Route::post('/careers/{jobPosition}', [CustomerJobController::class, 'apply'])
        ->whereNumber('jobPosition')
        ->name('recruitments.careers.apply');

    // Secure signed route to view/download applicant resume (requires authenticated web + signed link)
    Route::middleware(['auth', 'signed'])->group(function () {
        Route::get('/recruitments/applicants/{applicant}/resume', [ApplicantFileController::class, 'resume'])
            ->whereNumber('applicant')
            ->name('recruitments.applicants.resume');
    });
});
