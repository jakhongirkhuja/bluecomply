<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmploymentVerificationRequest extends FormRequest
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
            'driver_id' => 'required|exists:drivers,id',
            'company_id' => 'required|exists:companies,id',
            'direction' => 'required|in:outgoing,incoming',
            'employer_name' => 'required|string|max:255',
            'employer_usdot' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'fax_number' => 'nullable|string|max:50',
            'employment_start_date' => 'nullable|date',
            'employment_end_date' => 'nullable|date|after_or_equal:employment_start_date',
            'method' => 'required|in:email,fax',
            'notes' => 'nullable|string',
        ];
    }
}
