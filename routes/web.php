<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Leads\LeadController;
use App\Http\Controllers\Leads\LeadSourceController;
use App\Http\Controllers\Tenants\TenantOnboardingController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::get('onboarding/workspace', [TenantOnboardingController::class, 'create'])
        ->name('tenants.onboarding.create');

    Route::post('onboarding/workspace', [TenantOnboardingController::class, 'store'])
        ->name('tenants.onboarding.store');

    Route::middleware('tenant.workspace')->group(function () {
        Route::get('leads', [LeadController::class, 'index'])->name('leads.index');
        Route::post('leads', [LeadController::class, 'store'])->name('leads.store');
        Route::post('lead-sources', [LeadSourceController::class, 'store'])->name('lead-sources.store');
    });
});

require __DIR__.'/settings.php';
