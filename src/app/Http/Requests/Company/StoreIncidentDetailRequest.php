<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class StoreIncidentDetailRequest extends FormRequest
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
            'dot_reportable' => 'nullable|numeric|between:0,1',
            'injuries' => 'nullable|numeric|between:0,1',
            'injury_types' => 'required_if:injuries,1|array',
            'injury_types.*' => 'string|in:driver,passengers,thirdparty',

            'at_fault' => 'required|numeric|between:0,1',
            'preventable' => 'required|numeric|between:0,1',
            'fatalities' => 'nullable|numeric|between:0,1',



            'tow_required' => 'required|numeric|between:0,1',
            'towing_company_name' => 'required_if:tow_required,1|string|max:255',
            'towing_company_contact' => 'required_if:tow_required,1|string|max:255',
            'towing_company_address' => 'required_if:tow_required,1|string|max:255',

            'police_involved' => 'nullable|numeric|between:0,1',
            'police_report_number' => 'required_if:police_involved,1|string|max:255',
            'hazmat_release' => 'nullable|numeric|between:0,1',

            'damage_category' => 'required|array',
            'damage_category.*' => 'string|in:truck,trailer,thirdparty,equipment,cargo',
            'accident_description' => 'nullable|string',

            'post_accident_test' => 'nullable|numeric|between:0,1',
            'test_explanation' => 'nullable|string|required_if:post_accident_test,0',


        ];
    }
}
