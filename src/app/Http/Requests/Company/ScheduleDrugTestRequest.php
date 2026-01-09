<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleDrugTestRequest extends FormRequest
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
            'driver_id'       => 'required|exists:drivers,id',
            'expiration_date' => 'required|date|date_format:Y-m-d|after:today',
            'test_type'       => 'required|in:drug,alcohol,drug_alcohol',
            'reason'          => 'required|numeric|exists:drug_test_reasons,id',
            'package_code'    => 'nullable|string',
            'dot_agency'      => 'required_if:test_type,drug,drug_alcohol|nullable|exists:dot_agencies,id',
            'observed'        => 'required|numeric|between:0,1',
            'notes'           => 'nullable|string|max:500',
        ];
    }
}
