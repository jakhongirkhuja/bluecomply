<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmploymentVerificationResponseRequest extends FormRequest
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
            'status' => 'required|in:provided,denied,completed,denied-other',
            'description' => 'required_if:status,denied-other|string',
            'position_held' => 'required|string|max:255',
            'driver_class_id' => 'nullable|exists:cdlclasses,id',
            'driver_type' => 'required|string|in:local,regional,otr',
            'eligible_for_rehire' => 'required|string|in:yes,no,conditional',
            'was_terminated' => 'required|numeric|between:0,1',
            'termination_reason' => 'required_if:was_terminated,1|string|max:255',
            'fmcsr_subject' => 'required|numeric|between:0,1',
            'safety_sensitive_job' => 'required|numeric|between:0,1',
            'area_driven' => 'required|string|max:255',
            'equipment_driven' => 'required|string|in:tractortrailer,flatbed,reefer,tanker,boxtruck,straighttruck,sprintervan,hazmatvehicle',
            'trailer_driven' => 'required|string|in:dryvan,reefertrailer,flatbedtrailer,stepdeck,doubledrop,tankertrailer,intermodal,chassis,curtainside,puptrailer,doublestriples',
            'loads_hailed' => 'required|string|max:255',

            'alcohol_text_higher' => 'required|numeric|between:0,1',
            'verified_positive_drug_test' => 'required|numeric|between:0,1',
            'refused_test' => 'required|numeric|between:0,1',
            'other_dot_violation' => 'required|numeric|between:0,1',
            'reported_previous_violation' => 'required|numeric|between:0,1',
            'return_to_duty_completed' => 'required|numeric|between:0,1',
            'drug_alcohol_comments' => 'nullable|string',

            'accidents' => 'nullable|array',
            'accidents.*.accident_date' => 'required_with:accidents|date|date_format:Y-m-d',
            'accidents.*.dot_recordable' => 'required|numeric|between:0,1',
            'accidents.*.preventable' => 'required|numeric|between:0,1',
            'accidents.*.city' => 'required|string|max:255',
            'accidents.*.state_id' => 'required|numeric',
//            'accidents.*.state_id' => 'required|numeric|exists:states,id',
            'accidents.*.injuries' => 'required|integer|min:0',
            'accidents.*.fatalities' => 'required|integer|min:0',
            'accidents.*.hazardous_material_involved' => 'nullable|numeric|between:0,1',
            'accidents.*.equipment_driven' => 'nullable|string|max:255',
            'accidents.*.description' => 'nullable|string',
            'accidents.*.comments' => 'nullable|string',
        ];
    }
}
