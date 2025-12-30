<?php

namespace App\Http\Requests\Company;

use App\Models\Company\DocumentType;
use Illuminate\Foundation\Http\FormRequest;

class DriverDocumentRequest extends FormRequest
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
            'id'=>'nullable|exists:documents,id',
            'driver_id' => ['required', 'exists:drivers,id'],
            'document_type_id' => ['required', 'exists:document_types,id'],
        ];
    }
}
