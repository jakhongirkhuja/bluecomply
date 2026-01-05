<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class StoreIncidentCitationRequest extends FormRequest
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
            'driver_id'=>'required|numeric|exists:drivers,id',
            'citation_category_id' => 'required|numeric|exists:citation_categories,id',
            'citation_notes' => 'required|string|max:2000',
            'citation_number' => 'required|string|max:100',
            'issuing_agency_id' => 'required|exists:agencies,id',
            'citation_amount' => 'required|numeric|min:0|max:999999.99',
            'officer_name' => 'required|string|max:150',
            'court_date' => 'required|date|date_format:Y-m-d',
            'lawyer_hired' => 'required|numeric|between:0,1',
            'lawyer_name' => 'nullable|required_if:lawyer_hired,1|string|max:150',
            'lawyer_contact' => 'nullable|required_if:lawyer_hired,1|string|max:150',

        ];
    }
}
