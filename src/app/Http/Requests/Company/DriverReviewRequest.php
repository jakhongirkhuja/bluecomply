<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class DriverReviewRequest extends FormRequest
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
            'status'=>'required|string|in:approved,rejected',
            'hired_at'=>'required_if:status,approved|date|date_format:Y-m-d',
            'random_pool'=>'required_if:status,approved|numeric|between:0,1',
            'mvr_monitor'=>'required_if:status,approved|numeric|between:0,1',
            'rejection_reason_id'=>'required_if:status,rejected|numeric|exists:rejection_reasons,id',
            'description'=>'required_if:status,rejected|string|max:1000|min:20',
        ];
    }
}
