<?php

use Illuminate\Support\Facades\Route;
use Webkul\Recruitment\Http\Controllers\CustomerJobController;

Route::middleware(['web'])->group(function () {
    Route::get('/careers', [CustomerJobController::class, 'index'])
        ->name('recruitments.careers.index');

    Route::get('/careers/{jobPosition}', [CustomerJobController::class, 'show'])
        ->whereNumber('jobPosition')
        ->name('recruitments.careers.show');

    Route::post('/careers/{jobPosition}', [CustomerJobController::class, 'apply'])
        ->whereNumber('jobPosition')
        ->name('recruitments.careers.apply');
});
