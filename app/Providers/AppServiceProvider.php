<?php

namespace App\Providers;

use App\Models\FollowUp;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\Task;
use App\Models\User;
use App\Policies\FollowUpPolicy;
use App\Policies\LeadPolicy;
use App\Policies\LeadSourcePolicy;
use App\Policies\TaskPolicy;
use App\Support\PermissionRegistry;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->registerAuthorization();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    protected function registerAuthorization(): void
    {
        Gate::policy(Lead::class, LeadPolicy::class);
        Gate::policy(LeadSource::class, LeadSourcePolicy::class);
        Gate::policy(Task::class, TaskPolicy::class);
        Gate::policy(FollowUp::class, FollowUpPolicy::class);

        foreach (PermissionRegistry::permissions() as $permission) {
            Gate::define($permission, fn (User $user): bool => $user->hasTenantPermission($permission));
        }
    }
}
