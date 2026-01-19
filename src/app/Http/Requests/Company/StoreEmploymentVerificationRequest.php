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
            'id'=>'nullable|numeric|exists:employment_verifications,id',
            'driver_id' => 'required|exists:drivers,id',
            'direction' => 'required|in:outgoing,incoming',
            'employment_start_date' => 'required|date|date_format:Y-m-d',
            'employment_end_date' => 'required|date|date_format:Y-m-d|after_or_equal:employment_start_date',
            'method' => 'nullable|in:email,fax',
            'notes' => 'nullable|string',
        ];
    }
}
