<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UpdatePlanRequest extends FormRequest
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
        $planId = $this->route('plan');

        return [
            'name' => ['sometimes','required','string','max:255', Rule::unique('plans','name')->ignore($planId)],
            'description' => 'nullable|string|max:1000',
            'price' => 'sometimes|required|numeric|min:0|max:9999.99',
            'driver_limit' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
        ];
    }
}
