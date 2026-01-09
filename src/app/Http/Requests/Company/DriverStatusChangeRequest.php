<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class DriverStatusChangeRequest extends FormRequest
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
            'status'=>'required|string|in:active,home,donotdispatch,rehire',
            'hired_at'=>'required_if:status,rehire|date|date_format:Y-m-d',
            'send' =>'required_if:status,rehire|numeric|between:0,1',
        ];
    }
}
