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
            'employment_verification_id' => 'required|exists:employment_verifications,id',

            // Personal Info
            'position_held' => 'nullable|string|max:255',
            'driver_class' => 'nullable|string|max:255',
            'driver_type' => 'nullable|string|max:255',
            'eligible_for_rehire' => 'nullable|boolean',
            'was_terminated' => 'nullable|boolean',
            'termination_reason' => 'nullable|string|max:255',
            'fmcsr_subject' => 'nullable|boolean',
            'safety_sensitive_job' => 'nullable|boolean',
            'area_driven' => 'nullable|string|max:255',
            'equipment_driven' => 'nullable|string|max:255',
            'trailer_driven' => 'nullable|string|max:255',
            'loads_hailed' => 'nullable|string|max:255',

            // Drug & Alcohol
            'alcohol_0_04_or_higher' => 'nullable|boolean',
            'verified_positive_drug_test' => 'nullable|boolean',
            'refused_test' => 'nullable|boolean',
            'other_dot_violation' => 'nullable|boolean',
            'reported_previous_violation' => 'nullable|boolean',
            'return_to_duty_completed' => 'nullable|boolean',
            'drug_alcohol_comments' => 'nullable|string',

            // Accidents (array of accident objects)
            'accidents' => 'nullable|array',
            'accidents.*.accident_date' => 'required_with:accidents|date',
            'accidents.*.dot_recordable' => 'nullable|boolean',
            'accidents.*.preventable' => 'nullable|boolean',
            'accidents.*.city' => 'nullable|string|max:255',
            'accidents.*.state' => 'nullable|string|max:255',
            'accidents.*.injuries' => 'nullable|integer|min:0',
            'accidents.*.fatalities' => 'nullable|integer|min:0',
            'accidents.*.hazardous_material_involved' => 'nullable|boolean',
            'accidents.*.equipment_driven' => 'nullable|string|max:255',
            'accidents.*.description' => 'nullable|string',
            'accidents.*.comments' => 'nullable|string',
        ];
    }
}
