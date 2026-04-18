<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Leads\LeadController;
use App\Http\Controllers\Leads\LeadSourceController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\TenantAdmin\DashboardController as TenantAdminDashboardController;
use App\Http\Controllers\TenantAdmin\TeamController;
use App\Http\Controllers\TenantAdmin\TeamMemberController;
use App\Http\Controllers\TenantAdmin\TenantInvitationController;
use App\Http\Controllers\TenantInvitations\AcceptInvitationController;
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
        Route::get('tenant-admin/team', [TeamController::class, 'index'])->name('tenant-admin.team.index');
        Route::post('tenant-admin/invitations', [TenantInvitationController::class, 'store'])->name('tenant-admin.invitations.store');
        Route::delete('tenant-admin/invitations/{invitation}', [TenantInvitationController::class, 'destroy'])->name('tenant-admin.invitations.destroy');
        Route::patch('tenant-admin/team-members/{member}', [TeamMemberController::class, 'update'])->name('tenant-admin.team-members.update');
        Route::delete('tenant-admin/team-members/{member}', [TeamMemberController::class, 'destroy'])->name('tenant-admin.team-members.destroy');
    });

    Route::get('tenant-invitations/{token}', [AcceptInvitationController::class, 'show'])->name('tenant-invitations.accept.show');
    Route::post('tenant-invitations/{token}/accept', [AcceptInvitationController::class, 'store'])->name('tenant-invitations.accept.store');

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
