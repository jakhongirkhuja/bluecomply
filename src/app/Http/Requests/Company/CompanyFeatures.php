<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class CompanyFeatures extends FormRequest
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
            'claims_modal' => 'required|numeric|between:0,1',
            'roadside_inspections' => 'required|numeric|between:0,1',
            'drug_alcohol_testing' => 'required|numeric|between:0,1',
            'mvr_ordering' => 'required|numeric|between:0,1',
            'bulk_driver_import' => 'required|numeric|between:0,1',
        ];
    }
}
