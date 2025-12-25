<?php

namespace App\Http\Requests\Driver;

use Illuminate\Foundation\Http\FormRequest;

class DriverTerminationRequest extends FormRequest
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
            'termination_date' => 'required|date|date_format:Y-m-d',
            'payed_date' => 'nullable|date|date_format:Y-m-d',
            'termination_reason' => 'required|string|max:150',
            'rehire' => 'required|string',
            'notes' => 'nullable|string|max:2000',
            'notify_driver' => 'required|numeric|between:0,1',
        ];
    }
}
