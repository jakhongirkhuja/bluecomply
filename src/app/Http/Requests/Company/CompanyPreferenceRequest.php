<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class CompanyPreferenceRequest extends FormRequest
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
            'appearance' => 'required|in:light,dark,auto',
            'time_zone' => 'required|string|max:50',
            'language' => 'required|string|max:10',
            'date_format' => 'required|in:mm/dd/yyyy,dd/mm/yyyy,yyyy/mm/dd',
            'time_format' => 'required|in:12,24',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:10048',
            'signature' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}
