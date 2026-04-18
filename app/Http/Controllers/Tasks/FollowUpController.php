<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tasks\StoreFollowUpRequest;
use App\Models\FollowUp;
use Illuminate\Http\RedirectResponse;

class FollowUpController extends Controller
{
    public function store(StoreFollowUpRequest $request): RedirectResponse
    {
        $tenantId = $request->user()->current_tenant_id;
        $data = $request->validated();

        FollowUp::query()->create([
            'tenant_id' => $tenantId,
            'lead_id' => $data['lead_id'] ?? null,
            'task_id' => $data['task_id'] ?? null,
            'assigned_to_user_id' => $data['assigned_to_user_id'] ?? null,
            'created_by_user_id' => $request->user()->id,
            'scheduled_for' => $data['scheduled_for'],
            'status' => $data['status'],
            'notes' => $data['notes'] ?? null,
        ]);

        return back()->with('success', 'Follow-up created successfully.');
    }
}
