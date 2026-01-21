<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class VehicleDocumentRequest extends FormRequest
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
            'id'=>'nullable|exists:vehicle_documents,id',
            'type_id'=> 'required|exists:vehicle_types,id',
            'expires_at'=> 'required|date_format:Y-m-d',
            'description'=> 'required|string|max:500',
            'files'=> 'required_without:id|array',
            'files.*' => [
                'file',
                'mimes:pdf,png,jpg,jpeg',
                'max:10048'
            ],
        ];
    }
}
