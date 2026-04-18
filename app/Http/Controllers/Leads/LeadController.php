<?php

namespace App\Http\Controllers\Leads;

use App\Enums\LeadStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Leads\StoreLeadRequest;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class LeadController extends Controller
{
    public function index(): Response
    {
        $tenantId = request()->user()->current_tenant_id;

        $leads = Lead::query()
            ->where('tenant_id', $tenantId)
            ->with(['source:id,name', 'assignedTo:id,name'])
            ->latest()
            ->get()
            ->map(fn (Lead $lead) => [
                'id' => $lead->id,
                'name' => $lead->full_name,
                'email' => $lead->email,
                'primaryPhone' => $lead->primary_phone,
                'status' => $lead->status?->value,
                'source' => $lead->source?->name,
                'assignedTo' => $lead->assignedTo?->name,
                'createdAt' => $lead->created_at?->toDateTimeString(),
            ]);

        $leadSources = LeadSource::query()
            ->where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get(['id', 'name', 'description']);

        $teamMembers = User::query()
            ->select('users.id', 'users.name')
            ->join('tenant_user', 'tenant_user.user_id', '=', 'users.id')
            ->where('tenant_user.tenant_id', $tenantId)
            ->orderBy('users.name')
            ->get();

        return Inertia::render('leads/Index', [
            'leads' => $leads,
            'leadSources' => $leadSources,
            'teamMembers' => $teamMembers,
            'leadStatusOptions' => LeadStatus::values(),
            'stats' => [
                'total' => Lead::query()->where('tenant_id', $tenantId)->count(),
                'new' => Lead::query()->where('tenant_id', $tenantId)->where('status', LeadStatus::New->value)->count(),
                'contacted' => Lead::query()->where('tenant_id', $tenantId)->where('status', LeadStatus::Contacted->value)->count(),
                'qualified' => Lead::query()->where('tenant_id', $tenantId)->where('status', LeadStatus::Qualified->value)->count(),
            ],
        ]);
    }

    public function store(StoreLeadRequest $request): RedirectResponse
    {
        $tenantId = $request->user()->current_tenant_id;
        $data = $request->validated();

        Lead::query()->create([
            'tenant_id' => $tenantId,
            'lead_source_id' => $data['lead_source_id'] ?? null,
            'assigned_to_user_id' => $data['assigned_to_user_id'] ?? null,
            'created_by_user_id' => $request->user()->id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'] ?? null,
            'email' => $data['email'] ?? null,
            'primary_phone' => $data['primary_phone'],
            'status' => $data['status'],
            'notes' => $data['notes'] ?? null,
        ]);

        return back()->with('success', 'Lead created successfully.');
    }
}
