<?php

namespace App\Http\Controllers\TenantAdmin;

use App\Http\Controllers\Controller;
use App\Models\TenantInvitation;
use App\Models\User;
use App\Support\PermissionRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class TeamController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('team.manage');

        $tenant = $request->user()->currentTenant;
        $tenantId = $tenant?->id;

        $team = User::query()
            ->select('users.id', 'users.name', 'users.email', 'tenant_user.role', 'tenant_user.is_primary', 'tenant_user.joined_at')
            ->join('tenant_user', 'tenant_user.user_id', '=', 'users.id')
            ->where('tenant_user.tenant_id', $tenantId)
            ->orderByDesc('tenant_user.is_primary')
            ->orderBy('users.name')
            ->get()
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'isPrimary' => (bool) $user->is_primary,
                'joinedAt' => $user->joined_at,
                'isOwner' => $tenant?->owner_user_id === $user->id,
            ]);

        $pendingInvitations = TenantInvitation::query()
            ->where('tenant_id', $tenantId)
            ->whereNull('accepted_at')
            ->latest()
            ->get()
            ->map(fn (TenantInvitation $invitation) => [
                'id' => $invitation->id,
                'email' => $invitation->email,
                'role' => $invitation->role,
                'expiresAt' => $invitation->expires_at?->toDateTimeString(),
                'inviteLink' => route('tenant-invitations.accept.show', $invitation->token),
            ]);

        return Inertia::render('tenant-admin/Team', [
            'workspace' => [
                'name' => $tenant?->name,
                'slug' => $tenant?->slug,
            ],
            'roleOptions' => PermissionRegistry::assignableTenantRoles(),
            'team' => $team,
            'pendingInvitations' => $pendingInvitations,
        ]);
    }
}
