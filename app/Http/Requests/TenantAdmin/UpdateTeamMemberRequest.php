<?php

namespace App\Http\Requests\TenantAdmin;

use App\Support\PermissionRegistry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeamMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('team.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'role' => ['required', 'string', Rule::in(PermissionRegistry::assignableTenantRoles())],
        ];
    }
}
