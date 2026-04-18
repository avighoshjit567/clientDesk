<?php

namespace App\Http\Controllers\TenantAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TenantAdmin\UpdateTeamMemberRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class TeamMemberController extends Controller
{
    public function update(UpdateTeamMemberRequest $request, User $member): RedirectResponse
    {
        Gate::authorize('team.manage');

        $tenant = $request->user()->currentTenant;

        abort_unless($tenant?->users()->where('users.id', $member->id)->exists(), 404);

        if ($tenant->owner_user_id === $member->id) {
            return back()->withErrors([
                'role' => 'Workspace owner role cannot be changed.',
            ]);
        }

        $tenant->users()->updateExistingPivot($member->id, [
            'role' => $request->validated()['role'],
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Team role updated.'),
        ]);

        return to_route('tenant-admin.team.index');
    }

    public function destroy(Request $request, User $member): RedirectResponse
    {
        Gate::authorize('team.manage');

        $tenant = $request->user()->currentTenant;

        abort_unless($tenant?->users()->where('users.id', $member->id)->exists(), 404);

        if ($tenant->owner_user_id === $member->id) {
            return back()->withErrors([
                'member' => 'Workspace owner cannot be removed.',
            ]);
        }

        $tenant->users()->detach($member->id);

        if ($member->current_tenant_id === $tenant->id) {
            $member->forceFill([
                'current_tenant_id' => $member->tenants()->value('tenants.id'),
            ])->save();
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Team member removed.'),
        ]);

        return to_route('tenant-admin.team.index');
    }
}
