<?php

namespace App\Http\Requests\Tenants;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTenantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:120'],
            'subscription_plan_slug' => ['required', 'string', Rule::in(['starter', 'growth', 'scale'])],
            'timezone' => ['required', 'string', 'max:100'],
            'currency_code' => ['required', 'string', 'size:3'],
            'phone_country_code' => ['required', 'string', 'max:8'],
            'default_country' => ['required', 'string', 'size:2'],
        ];
    }
}
