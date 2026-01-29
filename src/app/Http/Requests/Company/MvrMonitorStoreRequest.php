<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class MvrMonitorStoreRequest extends FormRequest
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
            'driver_ids' => 'required|array|min:1',
            'driver_ids.*' => 'exists:drivers,id',
        ];
    }
    public function messages(): array
    {
        return [
            'driver_ids.required' => 'You must select at least one driver to enroll.',
            'driver_ids.*.exists' => 'One or more selected drivers do not exist.',
            'monthly_cost.required' => 'Monthly cost is required.',
            'monthly_cost.numeric' => 'Monthly cost must be a valid number.',
            'monthly_cost.min' => 'Monthly cost must be at least 0.',
        ];
    }
}
