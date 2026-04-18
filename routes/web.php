<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Leads\LeadController;
use App\Http\Controllers\Leads\LeadSourceController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\TenantAdmin\DashboardController as TenantAdminDashboardController;
use App\Http\Controllers\Tasks\FollowUpController;
use App\Http\Controllers\Tasks\TaskController;
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

    Route::middleware('super.admin')->group(function () {
        Route::get('super-admin/dashboard', SuperAdminDashboardController::class)->name('super-admin.dashboard');
    });

    Route::middleware(['tenant.workspace', 'tenant.admin'])->group(function () {
        Route::get('tenant-admin/dashboard', TenantAdminDashboardController::class)->name('tenant-admin.dashboard');
    });

    Route::middleware('tenant.workspace')->group(function () {
        Route::get('leads', [LeadController::class, 'index'])->name('leads.index');
        Route::post('leads', [LeadController::class, 'store'])->name('leads.store');
        Route::post('lead-sources', [LeadSourceController::class, 'store'])->name('lead-sources.store');
        Route::get('tasks', [TaskController::class, 'index'])->name('tasks.index');
        Route::post('tasks', [TaskController::class, 'store'])->name('tasks.store');
        Route::post('follow-ups', [FollowUpController::class, 'store'])->name('follow-ups.store');
    });
});

require __DIR__.'/settings.php';
