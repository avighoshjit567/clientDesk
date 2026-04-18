<?php

namespace App\Http\Requests\Tasks;

use App\Enums\FollowUpStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFollowUpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->current_tenant_id !== null;
    }

    public function rules(): array
    {
        $tenantId = $this->user()?->current_tenant_id;

        return [
            'scheduled_for' => ['required', 'date'],
            'status' => ['required', Rule::in(FollowUpStatus::values())],
            'notes' => ['nullable', 'string', 'max:2000'],
            'lead_id' => [
                'nullable',
                'integer',
                Rule::exists('leads', 'id')->where(fn ($query) => $query->where('tenant_id', $tenantId)),
            ],
            'task_id' => [
                'nullable',
                'integer',
                Rule::exists('tasks', 'id')->where(fn ($query) => $query->where('tenant_id', $tenantId)),
            ],
            'assigned_to_user_id' => [
                'nullable',
                'integer',
                Rule::exists('tenant_user', 'user_id')->where(fn ($query) => $query->where('tenant_id', $tenantId)),
            ],
        ];
    }
}
