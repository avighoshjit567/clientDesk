<?php

namespace App\Http\Requests\Leads;

use App\Enums\LeadStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->current_tenant_id !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $tenantId = $this->user()?->current_tenant_id;

        return [
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['nullable', 'string', 'max:80'],
            'email' => ['nullable', 'email', 'max:120'],
            'primary_phone' => ['required', 'string', 'max:30'],
            'status' => ['required', Rule::in(LeadStatus::values())],
            'notes' => ['nullable', 'string', 'max:2000'],
            'lead_source_id' => [
                'nullable',
                'integer',
                Rule::exists('lead_sources', 'id')->where(fn ($query) => $query->where('tenant_id', $tenantId)),
            ],
            'assigned_to_user_id' => [
                'nullable',
                'integer',
                Rule::exists('tenant_user', 'user_id')->where(fn ($query) => $query->where('tenant_id', $tenantId)),
            ],
        ];
    }
}
