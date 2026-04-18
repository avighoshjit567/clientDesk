<?php

namespace App\Http\Controllers\TenantAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TenantAdmin\StoreTenantInvitationRequest;
use App\Models\TenantInvitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Inertia\Inertia;

class TenantInvitationController extends Controller
{
    public function store(StoreTenantInvitationRequest $request): RedirectResponse
    {
        Gate::authorize('team.manage');

        $tenant = $request->user()->currentTenant;
        $email = Str::lower($request->validated()['email']);

        $memberExists = $tenant?->users()
            ->whereRaw('LOWER(users.email) = ?', [$email])
            ->exists();

        if ($memberExists) {
            return back()->withErrors([
                'email' => 'This user is already part of the workspace.',
            ]);
        }

        TenantInvitation::query()
            ->where('tenant_id', $tenant?->id)
            ->whereRaw('LOWER(email) = ?', [$email])
            ->whereNotNull('accepted_at')
            ->delete();

        $invitation = TenantInvitation::query()->updateOrCreate(
            [
                'tenant_id' => $tenant?->id,
                'email' => $email,
            ],
            [
                'role' => $request->validated()['role'],
                'token' => Str::random(64),
                'invited_by_user_id' => $request->user()->id,
                'expires_at' => now()->addDays(7),
                'accepted_at' => null,
            ],
        );

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Invitation created for :email', ['email' => $invitation->email]),
        ]);

        return to_route('tenant-admin.team.index');
    }

    public function destroy(Request $request, TenantInvitation $invitation): RedirectResponse
    {
        Gate::authorize('team.manage');

        abort_unless($invitation->tenant_id === $request->user()->current_tenant_id, 404);

        $invitation->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Invitation revoked.'),
        ]);

        return to_route('tenant-admin.team.index');
    }
}
