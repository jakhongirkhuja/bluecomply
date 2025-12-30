<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class AddImageToDocumentRequest extends FormRequest
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
            'document_id' => 'required|exists:documents,id',
            'driver_id' => 'required|exists:drivers,id',
            'files' =>'required|array',
            'files.*' => ['file','mimes:pdf,png,jpg,jpeg','max:10048'],
        ];
    }
}
