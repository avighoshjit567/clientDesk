<?php

namespace App\Http\Controllers\TenantInvitations;

use App\Http\Controllers\Controller;
use App\Models\TenantInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AcceptInvitationController extends Controller
{
    public function show(Request $request, string $token): Response
    {
        $invitation = $this->resolveInvitation($request, $token);

        return Inertia::render('tenant-invitations/Accept', [
            'invitation' => [
                'tenantName' => $invitation->tenant?->name,
                'email' => $invitation->email,
                'role' => $invitation->role,
                'expiresAt' => $invitation->expires_at?->toDateTimeString(),
            ],
        ]);
    }

    public function store(Request $request, string $token): RedirectResponse
    {
        $invitation = $this->resolveInvitation($request, $token);
        $tenant = $invitation->tenant;
        $user = $request->user();

        $tenant?->users()->syncWithoutDetaching([
            $user->id => [
                'role' => $invitation->role,
                'is_primary' => false,
                'joined_at' => now(),
                'invited_by_user_id' => $invitation->invited_by_user_id,
            ],
        ]);

        if ($user->current_tenant_id === null) {
            $user->forceFill([
                'current_tenant_id' => $tenant?->id,
            ])->save();
        }

        $invitation->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Invitation accepted. Welcome to :workspace.', ['workspace' => $tenant?->name]),
        ]);

        return to_route('dashboard');
    }

    private function resolveInvitation(Request $request, string $token): TenantInvitation
    {
        $invitation = TenantInvitation::query()
            ->with('tenant:id,name')
            ->where('token', $token)
            ->whereNull('accepted_at')
            ->firstOrFail();

        abort_if($invitation->expires_at?->isPast(), 410, 'This invitation has expired.');
        abort_unless(strtolower($request->user()->email) === strtolower($invitation->email), 403);

        return $invitation;
    }
}
