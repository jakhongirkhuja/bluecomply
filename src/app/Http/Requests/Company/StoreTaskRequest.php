<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'driver_id'=>'required|exists:drivers,id',
            'related_type' => 'nullable|string|in:trailer,truck,asset',
            'related_id' => 'nullable|string',
            'due_date' => 'required|date|date_format:Y-m-d',
            'priority' => 'required|in:low,medium,high',
            'attachments'=>'nullable|array',
            'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx',
        ];
    }
}
