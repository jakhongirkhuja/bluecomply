<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class UploadDocumentRequest extends FormRequest
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
            'document_type_id'=>'required|numeric|exists:vehicle_insurance_types,id',
            'company_type_id'=>'required|numeric|exists:compliance_categories,id',
            'expires_at'=>'required|date_format:Y-m-d',
            'related_to'=>'required|string',
        ];
    }
}
