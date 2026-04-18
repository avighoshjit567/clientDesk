<?php

namespace App\Http\Controllers\Leads;

use App\Http\Controllers\Controller;
use App\Http\Requests\Leads\StoreLeadSourceRequest;
use App\Models\LeadSource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class LeadSourceController extends Controller
{
    public function store(StoreLeadSourceRequest $request): RedirectResponse
    {
        $tenantId = $request->user()->current_tenant_id;
        $name = $request->validated()['name'];
        $baseSlug = Str::slug($name) ?: 'source';
        $slug = $baseSlug;
        $counter = 2;

        while (LeadSource::query()->where('tenant_id', $tenantId)->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        LeadSource::query()->create([
            'tenant_id' => $tenantId,
            'name' => $name,
            'slug' => $slug,
            'description' => $request->validated()['description'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', 'Lead source created successfully.');
    }
}
