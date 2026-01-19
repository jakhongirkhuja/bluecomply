<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company_name' => 'required|string|max:255',
            'tenet_id' => 'required|string|max:50|unique:companies,tenet_id',
            'dot_number' => 'required|string|max:50|unique:companies,dot_number',
            'status'       => 'required|in:active,trial,suspended',
            'der_name' => 'required|string|max:255',
            'der_email' => 'required|email|max:255',
            'der_phone' => 'required|string|max:50',
            'plan_id' => 'required_if:status,active|exists:plans,id',
            'drivers' => 'required|integer|min:0',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:50',
            'phone' => 'required|string|unique:users,phone|max:50',
            'notes'=>'nullable|string|max:10000',
        ];
    }
}
