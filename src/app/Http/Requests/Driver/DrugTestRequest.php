<?php

namespace App\Http\Requests\Driver;

use Illuminate\Foundation\Http\FormRequest;

class DrugTestRequest extends FormRequest
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
            'test_type' => 'required|string|in:Urine Drug,Breath Alcohol',
            'random_pool_membership' => 'nullable|numeric|between:0,1',
            'reason' => 'required|string|max:100',
            'requested_date' => 'required|date|date_format:Y-m-d',
            'collected_date' => 'nullable|date|date_format:Y-m-d',
            'completed_date' => 'nullable|date|date_format:Y-m-d',
            'result' => 'nullable|string|in:Positive,Negative,N/A',
        ];
    }
}
