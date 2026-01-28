<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyOwnerEditRequest extends FormRequest
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
            'company_name'      => 'required|string|max:255',
            'dot_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('companies', 'dot_number')
                    ->ignore($this->route('company_id')),
            ],
            'email'             => 'required|email|max:255',
            'address'           => 'required|string|max:255',
            'city'              => 'required|string|max:100',
            'state_id'          => 'required|exists:states,id',
            'zip_code'          => 'required|string|max:20',
            'current_password' => 'required_with:password|current_password',
            'password' => 'nullable|string|min:6|confirmed',
            'sms_2fa_enabled'=>'required|numeric|in:0,1',
            'totp_enabled'=>'required|numeric|in:0,1',
        ];
    }
}
